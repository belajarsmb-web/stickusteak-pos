const fs = require('fs');
const log = fs.readFileSync('c:/Project/restopos/restopos/backend/logs/error.log', 'utf8');
const lines = log.trim().split('\n');
const lastLine = lines[lines.length - 1];
try {
    const json = JSON.parse(lastLine);
    console.log("MESSAGE:", json.message);
} catch (e) {
    console.log("RAW:", lastLine.substring(0, 500));
}
