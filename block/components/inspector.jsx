import { get, kebabCase } from "lodash";
const { compose } = wp.compose;
const { Component } = wp.element;
const { withSpokenMessages, ComboboxControl, PanelBody, PanelRow } =
  wp.components;
const { __ } = wp.i18n;

const { InspectorControls } = wp.blockEditor;
import { CloudDownloadIcon } from "@heroicons/react/solid";
import * as api from "../../lib/api";

class Inspector extends Component {
  constructor(props) {
    super(...arguments);
    this.props = props;

    this.state = {
      value: null,
      loading: true,
      episodes: [],
    };
  }

  computeId({ site, title, episode, post }) {
    if (!episode || !site) {
      return null;
    }

    return kebabCase(`${site} ${episode}`);
  }

  computeLabel({ site, title, episode, post }) {
    return `[${site}] ${title}`;
  }

  componentDidMount() {
    api.get("episodes").then(({ results }) => {
      this.setState({
        loading: false,
        value: this.computeId(this.props.attributes),
        episodes: results.map((episode) => ({
          ...episode,
          id: this.computeId(episode),
        })),
      });
    });
  }

  selectEpisode(value) {
    const episode = this.state.episodes.find((episode) => episode.id === value);
    this.setState({
        value
    });
    
    if (!episode) {
      this.props.setAttributes({ value });
      return;
    }

    api
      .get("details", { site: episode.site, id: episode.episode })
      .then(({ result }) => {

        this.props.setAttributes({
          post: get(result, "post_id", "").toString(),
          episode: get(result, "id", "").toString(),
          site: episode.site,
          audio: get(result, ["audio", 0, "url"]),
          title: get(result, "title"),
        });
      });
  }

  render() {
    const loadingStyle = {
      display: "flex",
    };

    const iconStyle = { marginRight: "5px", marginTop: "2px" };

    const loading = (
      <div style={loadingStyle}>
        <CloudDownloadIcon width={18} height={18} style={iconStyle} />
        <span>{__("Fetching Data", "podlove-player-aggregator")}</span>
      </div>
    );

    const select = (
      <ComboboxControl
        label={__("Episode", "podlove-player-aggregator")}
        value={this.state.value}
        onChange={this.selectEpisode.bind(this)}
        options={this.state.episodes.map((episode) => ({
          label: this.computeLabel(episode),
          value: episode.id,
        }))}
      />
    );

    return (
      <InspectorControls>
        <PanelBody title={__("Settings", "podlove-player-aggregator")}>
          <PanelRow>{this.state.loading ? loading : select}</PanelRow>
        </PanelBody>
      </InspectorControls>
    );
  }
}

export default compose([withSpokenMessages])(Inspector);
