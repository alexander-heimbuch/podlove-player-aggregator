const path = require('path');
const { copy, mkdirp, emptyDir } = require('fs-extra');
const { zip } = require('zip-a-folder')

const FILES = [
  "admin",
  "block",
  "includes",
  "languages",
  "lib",
  "public",
  "index.php",
  "podlove-player-aggregator.php",
  "uninstall.php",
];

const DEST = 'dist/podlove-player-aggregator';

const ZIP = 'dist/podlove-player-aggregator.zip'


async function main() {
    await mkdirp(DEST);
    await emptyDir(DEST);
    await Promise.all(FILES.map(file => copy(file, path.join(DEST, file))))
    await zip(DEST, ZIP);
}

main();