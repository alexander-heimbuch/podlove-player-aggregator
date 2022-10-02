import { get, kebabCase } from "lodash";
const { compose } = wp.compose;
const { Component } = wp.element;
const { withSpokenMessages, ComboboxControl, PanelBody, PanelRow, SelectControl } =
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
      configs: [],
      themes: [],
      templates: []
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

      if (this.props.attributes.episode) {
        this.selectEpisode(this.computeId(this.props.attributes));
      }
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
          title: get(result, "title")
        });

        this.setState({
          configs: get(result, ["playerOptions", "configs"], []),
          themes: get(result, ["playerOptions", "themes"], []),
          templates: get(result, ["playerOptions", "templates"], [])
        });

        this.selectEmbed(this.props.attributes.embed || 'audio');
      });
  }

  selectEmbed(embed) {
    const defaultConfig = this.props.attributes.config || get(this.state, ['configs', 0], null)
    const defaultTheme = this.props.attributes.theme || get(this.state, ['themes', 0], null)
    const defaultTemplate = this.props.attributes.template || get(this.state, ['templates', 0], null)

    const onPlayer = (value) => embed === 'player' ? value : null

    this.props.setAttributes({ 
      embed, theme: onPlayer(defaultTheme), config: onPlayer(defaultConfig), template: onPlayer(defaultTemplate)
    });
  }

  selectAttribute(attribute) {
    return (value) => {
      this.props.setAttributes({ [attribute]: value });
    }
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
      <div>
        <div style={{marginBottom: "24px"}}>
          <ComboboxControl
            label={__("Episode", "podlove-player-aggregator")}
            value={this.state.value}
            onChange={this.selectEpisode.bind(this)}
            options={this.state.episodes.map((episode) => ({
              label: this.computeLabel(episode),
              value: episode.id,
            }))}
          />
        </div>
        <SelectControl
            label={__("Embed", "podlove-player-aggregator")}
            value={ this.props.attributes.embed }
            options={ [
              { label: 'Audio Element', value: 'audio' },
              ...(this.state.configs.length > 0 ? [{ label: 'Web Player', value: 'player' }]: []),
            ] }
            onChange={this.selectEmbed.bind(this)}
        />

        {(this.state.configs.length > 0 && this.props.attributes.embed === 'player') &&
          <SelectControl
              label={__("Player Config", "podlove-player-aggregator")}
              value={ this.props.attributes.config }
              options={ this.state.configs.map(config => ({ value: config, label: config })) }
              onChange={this.selectAttribute('config')}
          />
        }

        {(this.state.themes.length > 0 && this.props.attributes.embed === 'player') &&
          <SelectControl
              label={__("Player Theme", "podlove-player-aggregator")}
              value={ this.props.attributes.theme }
              options={ this.state.themes.map(theme => ({ value: theme, label: theme })) }
              onChange={this.selectAttribute('theme')}
          />
        }

        {(this.state.templates.length > 0 && this.props.attributes.embed === 'player') &&
          <SelectControl
              label={__("Player Templates", "podlove-player-aggregator")}
              value={ this.props.attributes.template }
              options={ this.state.templates.map(template => ({ value: template, label: template })) }
              onChange={this.selectAttribute('template')}
          />
        }
      </div>
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
