const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const publicDir = path.join(root, 'public');
const buildDir = path.join(root, 'build');
const files = ['index.html', 'styles.css', 'logo.jpg', 'procesar.php'];

fs.rmSync(buildDir, { recursive: true, force: true });
fs.mkdirSync(buildDir, { recursive: true });

for (const file of files) {
  fs.copyFileSync(path.join(publicDir, file), path.join(buildDir, file));
}

console.log(`Build estatico generado en ${buildDir}`);
