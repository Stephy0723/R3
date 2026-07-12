const fs = require('fs');
const path = require('path');

const publicDir = path.resolve(__dirname, '..', 'public');
const requiredFiles = ['index.html', 'styles.css', 'logo.jpg', 'procesar.php'];
const errors = [];

for (const file of requiredFiles) {
  if (!fs.existsSync(path.join(publicDir, file))) {
    errors.push(`Falta public/${file}`);
  }
}

const index = fs.readFileSync(path.join(publicDir, 'index.html'), 'utf8');

for (const asset of ['styles.css', 'logo.jpg', 'procesar.php']) {
  if (!index.includes(asset)) {
    errors.push(`index.html no referencia ${asset}`);
  }
}

if (/formulario\.(html|php)|FormularioCredito3R\.css/.test(index)) {
  errors.push('index.html todavia referencia archivos antiguos o duplicados');
}

if (errors.length > 0) {
  console.error(errors.join('\n'));
  process.exit(1);
}

console.log('Validacion estatica correcta');
