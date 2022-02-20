const { __ } = wp.i18n;

const { render, Component, Fragment } = wp.element;

import { DocumentAddIcon, PlusIcon } from "@heroicons/react/solid";
import "./style.css";

import { Site } from "./site";
import * as api from "../../lib/api";

class App extends Component {
  constructor() {
    super(...arguments);

    this.state = {
      loading: true,
      sites: [],
    };
  }

  saveSites() {
    api.post("sites", {
      sites: this.state.sites.map((site) => ({
        name: site.name,
        url: site.url,
      })),
    });
  }

  async addSite() {
    await this.setState({
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
    this.saveSites();
  }

  updateSite(index) {
    return async (update) => {
      await this.setState({
        sites: this.state.sites.map((site) => {
          if (site.index === index) {
            return {
              ...site,
              ...update,
              mode: "pending",
            };
          }
          return site;
        }),
      });
      this.saveSites();
	  this.checkAvailablility(index);
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
    return async () => {
      if (!confirm(__("Do you really want to delete this site?"))) {
        return;
      }

      await this.setState({
        sites: this.state.sites.filter((site) => site.index !== index),
      });

      this.saveSites();
    };
  }

  checkAvailablility(index) {
    const site = this.state.sites.find((site) => site.index === index);
    api.post("verify", { site: site.url }).then(({ valid }) => {
      this.setState({
        sites: this.state.sites.map((site) => {
          if (site.index !== index) {
            return site;
          }

          return {
            ...site,
            state: valid ? "valid" : "invalid",
          };
        }),
      });
    });
  }

  componentDidMount() {
    api
      .get("sites")
      .then(async (sites) => {
        await this.setState({
          loading: false,
          sites: sites.map((site, index) => ({
            index,
            mode: "show",
            state: "pending",
            ...site,
          })),
        });
      })
      .then(() => {
        this.state.sites.forEach((site) => {
          this.checkAvailablility(site.index);
        });
      });
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
        {this.state.loading === false && (
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
            {this.state.sites.length > 0 && (
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
            )}
            {this.state.sites.length === 0 && (
              <div className="text-center">
                <svg
                  className="mx-auto h-12 w-12 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  aria-hidden="true"
                >
                  <path
                    vectorEffect="non-scaling-stroke"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                  />
                </svg>
                <h3 className="mt-2 text-sm font-medium text-gray-900">
                  {__("No Sites")}
                </h3>
                <p className="mt-1 text-sm text-gray-500">
                  {__("Get started by creating a new project.")}
                </p>
                <div className="mt-6">
                  <button
                    type="button"
                    className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    onClick={this.addSite.bind(this)}
                  >
                    <PlusIcon
                      className="-ml-1 mr-2 h-5 w-5"
                      aria-hidden="true"
                    />
                    {__("Add Podlove Site")}
                  </button>
                </div>
              </div>
            )}
          </div>
        )}
      </Fragment>
    );
  }
}

render(<App />, document.getElementById("podlove-player-aggregator-settings"));
