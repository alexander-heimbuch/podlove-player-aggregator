const { __ } = wp.i18n;

const { render, Component, Fragment } = wp.element;

import { DocumentAddIcon } from "@heroicons/react/solid";
import "./style.css";

import { Site } from "./site";

class App extends Component {
  constructor() {
    super(...arguments);

    this.state = {
      sites: [
        {
          index: 0,
          state: "valid",
          name: "Valid Site",
          url: "http://valid-url.com",
          mode: "show",
        },
        {
          index: 1,
          state: "invalid",
          name: "Invalid Site",
          url: "http://invalid-url.com",
          mode: "show",
        },
        {
          index: 2,
          state: "invalid",
          name: "Valid Site",
          url: "http://valid-url.com",
          mode: "edit",
        },
        {
          index: 3,
          state: "pending",
          name: "Pending Site",
          url: "http://valid-url.com",
          mode: "show",
        },
      ],
    };
  }

  addSite() {
    this.setState({
      sites: [
        ...this.state.sites,
        {
          index: this.state.sites.length,
          state: "pending",
          name: null,
          url: null,
          mode: "edit",
        },
      ],
    });
  }

  updateSite(index) {
    return (update) => {
      this.setState({
        sites: this.state.sites.map((site) => {
          if (site.index === index) {
            return {
              ...site,
              ...update,
              mode: "show",
            };
          }
          return site;
        }),
      });
    };
  }

  cancelSiteUpdate(index) {
    return () => {
      this.setState({
        sites: this.state.sites.map((site) => {
          if (site.index === index) {
            return {
              ...site,
              mode: "show",
            };
          }
          return site;
        }),
      });
    };
  }

  editSite(index) {
    return () => {
      this.setState({
        sites: this.state.sites.map((site) => {
          if (site.index === index) {
            return {
              ...site,
              mode: "edit",
            };
          }
          return site;
        }),
      });
    };
  }

  deleteSite(index) {
    return () => {
      confirm(__("Do you really want to delete this site?")) &&
        this.setState({
          sites: this.state.sites.filter((site) => site.index !== index),
        });
    };
  }

  async componentDidMount() {
	  await fetch(window.PODLOVE_PLAYER_AGGREGATOR.api.sites, {
		headers: {
		  'X-WP-Nonce': window.PODLOVE_PLAYER_AGGREGATOR.nonce
		},
	  }).then(result => result.json()).then(console.log);
  }

  render() {
    return (
      <Fragment>
        <div className="py-5 px-2 bg-white shadow-sm mb-6">
          <div className="mx-auto my-0 max-w-3xl">
            <div className="flex items-center content-center">
              <h1 className="font-sans text-2xl">
                {__("Podlove Player Aggregator")}
              </h1>
            </div>
          </div>
        </div>

        <div className="ml-auto mr-auto max-w-3xl">
          <div className="bg-white shadow overflow-hidden sm:rounded-md mb-5">
            <ul role="list" className="divide-y divide-gray-200">
              {this.state.sites.map((site, index) => (
                <li>
                  <Site
                    key={`site-${index}`}
                    state={site.state}
                    name={site.name}
                    url={site.url}
                    mode={site.mode}
                    onSiteUpdate={this.updateSite(site.index)}
                    onSiteCancel={this.cancelSiteUpdate(site.index)}
                    onSiteEdit={this.editSite(site.index)}
                    onSiteDelete={this.deleteSite(site.index)}
                  />
                </li>
              ))}
            </ul>
          </div>
          <div class="flex flex-col items-end">
            <button
              type="button"
              className="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              onClick={this.addSite.bind(this)}
            >
              <DocumentAddIcon
                className="-ml-0.5 mr-2 h-4 w-4"
                aria-hidden="true"
              />
              {__("Add Podlove Site")}
            </button>
          </div>
        </div>
      </Fragment>
    );
  }
}

render(<App />, document.getElementById("podlove-player-aggregator-settings"));
