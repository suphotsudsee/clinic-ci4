# Card Reader Service (Local)

## Purpose
Local service that runs on clinic machine and communicates with smart card middleware/driver.

## Run
1. cd card-reader-service
2. npm install
3. npm run start

Default endpoint: `http://127.0.0.1:8888/read`

## Production design
- Service runs as local daemon on front-desk PC.
- Service reads card via hardware SDK and returns JSON only over localhost.
- CI4 backend calls service endpoint from `Api\CardController`.
- If service fails, UI allows manual input fallback.
