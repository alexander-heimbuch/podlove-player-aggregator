import logo from './components/logo'

const { __ } = wp.i18n
const { registerBlockType } = wp.blocks

registerBlockType('podlove-player-aggregator/shortcode', {
  title: __('Podlove Web Player', 'podlove-web-player'),
  description: __('HTML 5 Podcast Player', 'podlove-web-player'),
  icon: logo(),
  category: 'embed',

  attributes: {
    site: {
      type: 'string',
    },

    title: {
      type: 'string',
    },

    episode: {
      type: 'string',
    },

    post: {
      type: 'string',
    }
  },

  edit: props => (
    <div className={props.className}>
        Hello World!
    </div>
  ),

  save() {
    return null
  },
})
