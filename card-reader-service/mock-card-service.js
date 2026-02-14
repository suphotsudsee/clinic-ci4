const express = require('express');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

app.get('/health', (req, res) => {
  res.json({ ok: true, service: 'card-reader', ts: new Date().toISOString() });
});

app.get('/read-card', (req, res) => {
  // Replace with real smart-card SDK implementation in production.
  res.json({
    cid: '1103700000001',
    title_name: 'นาย',
    first_name: 'ทดสอบ',
    last_name: 'ระบบ',
    gender: 'M',
    dob: '1990-01-01',
    address: '99/9 กรุงเทพมหานคร'
  });
});

const PORT = process.env.PORT || 8787;
app.listen(PORT, () => {
  console.log(`Card reader service listening on http://127.0.0.1:${PORT}`);
});
