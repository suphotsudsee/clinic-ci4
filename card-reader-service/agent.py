import base64
import codecs
import threading
import sys
from flask import Flask, jsonify
from flask_cors import CORS
from smartcard.System import readers
from PIL import Image, ImageDraw
import pystray

app = Flask(__name__)
CORS(app)


def decode_thai(data):
    try:
        clean_data = bytes([x for x in data if x != 0x00])
        return codecs.decode(clean_data, 'tis-620').strip().replace('#', ' ')
    except Exception:
        return ""


def get_full_card_data():
    try:
        r = readers()
        if not r:
            return {"success": False, "message": "ไม่พบเครื่องอ่านบัตร"}

        conn = r[0].createConnection()
        conn.connect()

        # Select Thai national ID applet
        _, sw1, sw2 = conn.transmit([
            0x00, 0xA4, 0x04, 0x00, 0x08,
            0xA0, 0x00, 0x00, 0x00, 0x54, 0x48, 0x00, 0x01,
        ])
        if sw1 == 0x61:
            # Some readers return 61xx, requiring GET RESPONSE.
            _, sw1, sw2 = conn.transmit([0x00, 0xC0, 0x00, 0x00, sw2])
        if sw1 != 0x90:
            return {"success": False, "message": f"เลือกแอปบัตรไม่สำเร็จ (SW={sw1:02X}{sw2:02X})"}

        def transmit_with_get_response(apdu):
            data, sw1, sw2 = conn.transmit(apdu)
            if sw1 == 0x61:
                data, sw1, sw2 = conn.transmit([0x00, 0xC0, 0x00, 0x00, sw2])
            if sw1 == 0x90:
                return data
            return []

        def read_data(offset, length):
            p1, p2 = (offset >> 8) & 0xFF, offset & 0xFF
            # Different readers may require different APDU formats.
            apdus = [
                [0x80, 0xB0, p1, p2, 0x02, 0x00, length],
                [0x80, 0xB0, p1, p2, length],
            ]
            for apdu in apdus:
                data = transmit_with_get_response(apdu)
                if data:
                    return data
            return []

        def extract_jpeg(raw_bytes):
            if not raw_bytes:
                return b""
            start = raw_bytes.find(b"\xFF\xD8")
            end = raw_bytes.rfind(b"\xFF\xD9")
            if start == -1 or end == -1 or end <= start:
                return b""
            return raw_bytes[start : end + 2]

        def read_photo_data():
            # Thai ID card photo starts at 0x017B and is read in 0xFC-byte chunks.
            photo = bytearray()
            for i in range(20):
                offset = 0x017B + (i * 0x00FC)
                p1, p2 = (offset >> 8) & 0xFF, offset & 0xFF
                chunk = transmit_with_get_response([0x80, 0xB0, p1, p2, 0x02, 0x00, 0xFC])
                if not chunk:
                    break
                photo.extend(chunk)
            return extract_jpeg(bytes(photo))

        cid = decode_thai(read_data(0x0004, 0x0D))
        name_th = decode_thai(read_data(0x0011, 0x64))
        name_en = decode_thai(read_data(0x0075, 0x64))
        dob = decode_thai(read_data(0x00D9, 0x08))
        address = decode_thai(read_data(0x1579, 0x96))

        if not cid:
            return {
                "success": False,
                "message": "อ่านบัตรไม่สำเร็จ: ไม่พบเลขบัตร (ตรวจการเสียบบัตร/driver)",
            }

        photo_bytes = read_photo_data()
        photo_data_url = ""
        if photo_bytes:
            photo_data_url = f"data:image/jpeg;base64,{base64.b64encode(photo_bytes).decode('utf-8')}"

        return {
            "success": True,
            "data": {
                "cid": cid,
                "name_th": name_th,
                "name_en": name_en,
                "dob": dob,
                "address": address,
                "photo": photo_data_url,
            },
        }
    except Exception as e:
        return {"success": False, "message": str(e)}


@app.route('/read', methods=['GET'])
def api_read():
    return jsonify(get_full_card_data())


def create_image():
    image = Image.new('RGB', (64, 64), color=(0, 122, 204))
    dc = ImageDraw.Draw(image)
    dc.rectangle([16, 16, 48, 48], fill=(255, 255, 255))
    return image


def run_tray():
    icon = pystray.Icon("ThaiIDAgent", create_image(), "Thai ID Agent (Port 8888)")
    icon.menu = pystray.Menu(pystray.MenuItem('Exit Agent', lambda: sys.exit()))
    icon.run()


if __name__ == '__main__':
    threading.Thread(target=run_tray, daemon=True).start()
    app.run(host='127.0.0.1', port=8888)
