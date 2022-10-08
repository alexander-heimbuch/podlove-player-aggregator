const replace = require("replace-in-file");
const ENTRY = "podlove-player-aggregator.php";
const version = require("../package.json").version;

async function main() {
  await replace({
    files: ENTRY,
    from: /(Version:           \d\.\d\.\d[-]?[\d])/,
    to: `Version:           ${version}`,
  });

  await replace({
    files: ENTRY,
    from: /(define\( \'PODLOVE_PLAYER_AGGREGATOR_VERSION\'\, \'\d\.\d\.\d[-]?[\d]\' \))/,
    to: `define( 'PODLOVE_PLAYER_AGGREGATOR_VERSION', '${version}' )`,
  });
}

main();