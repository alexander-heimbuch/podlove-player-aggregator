const { __ } = wp.i18n;

const {
	BaseControl,
	Button,
	ExternalLink,
	PanelBody,
	PanelRow,
	Placeholder,
	Spinner,
	ToggleControl
} = wp.components;

const {
	render,
	Component,
	Fragment
} = wp.element;


class App extends Component {
	render() {
		return (
			<Placeholder>
				<Spinner/>
			</Placeholder>
		)
	}
}

render(
	<App/>,
	document.getElementById( 'podlove-player-aggregator-settings' )
);