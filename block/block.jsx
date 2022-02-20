import logo from "./components/logo";
import Inspector from "./components/inspector";
import Placeholder from "./components/placeholder";

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

window.PodlovePlayerAggregator = {
  episodes: [],
  loading: true
}

registerBlockType("podlove-player-aggregator/shortcode", {
  title: __("Podlove Aggregator Player", "podlove-player-aggregator"),
  description: __(
    "Search and embed Podlove Episodes",
    "podlove-player-aggregator"
  ),
  icon: logo(40),
  category: "embed",

  attributes: {
    site: {
      type: "string",
    },

    title: {
      type: "string",
    },

    episode: {
      type: "string",
    },

    post: {
      type: "string",
    },

    audio: {
      type: "string"
    }
  },

  edit: (props) => (
    <div className={props.className}>
      <Inspector {...props} />
      <Placeholder {...props} />
    </div>
  ),

  save() {
    return null;
  },
});
