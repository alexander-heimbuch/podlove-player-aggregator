import logo from "./logo";
const { Component, Fragment } = wp.element;
const { compose } = wp.compose;
const { withSpokenMessages } = wp.components;
const { withDispatch } = wp.data;
const { __ } = wp.i18n;

class Placeholder extends Component {
  render() {
    const containerStyle = {
      width: "100%",
      display: "flex",
      justifyContent: "center",
      alignItems: "center",
      background: "white",
      flexDirection: "column",
      padding: "15px",
    };

    const headlineStyle = {
      fontSize: "1.25em",
      width: "350px",
      whiteSpace: "nowrap",
      overflow: "hidden",
      textOverflow: "ellipsis",
    };

    const episodeStyle = {
      width: "100%",
      display: "flex",
      justifyContent: "center",
      alignItems: "center",
    };

    const titleStyle = {
      textAlign: "left",
    };

    const logoStyle = {
      marginRight: "15px",
      marginTop: "7px",
    };

    const placeholderLogoStyle = {
      marginBottom: "15px",
    };

    const emptyPlaceholder = (
      <Fragment>
        <div style={placeholderLogoStyle}>{logo(50, 1)}</div>
        <span style={headlineStyle}>
          {__("Select Podcast Episode", "podlove-player-aggregator")}
        </span>
      </Fragment>
    );

    const episodePlaceholder = (
      <div style={episodeStyle}>
        <div style={logoStyle}>{logo(50, 1)}</div>
        <div style={titleStyle}>
          <div style={headlineStyle}>{this.props.attributes.title}</div>
          <div>{this.props.attributes.site}</div>
        </div>
      </div>
    );

    return (
      <button
        onClick={this.props.openBlock(this.props.clientId)}
        style={containerStyle}
      >
        {this.props.attributes.episode ? episodePlaceholder : emptyPlaceholder}
      </button>
    );
  }
}

export default compose([
  withSpokenMessages,
  withDispatch((dispatch) => {
    return {
      openBlock: (id) => () => {
        dispatch("core/block-editor").selectBlock(id);
        dispatch("core/edit-post").openGeneralSidebar("edit-post/block");
      },
    };
  }),
])(Placeholder);
