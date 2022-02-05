const { __ } = wp.i18n;

const { Component, Fragment } = wp.element;

import "./style.css";

import {
  CheckCircleIcon,
  PencilIcon,
  TrashIcon,
  ExclamationCircleIcon,
  CheckIcon,
  XIcon,
} from "@heroicons/react/solid";

export class Site extends Component {
  constructor(props) {
    super(...arguments);
    this.state = {
      name: props.name,
      url: props.url,
    };
  }

  render() {
    let siteState;
    let siteControls;
    let siteDescription;

    switch (this.props.state) {
      case "valid":
        siteState = <CheckCircleIcon className="w-7 text-green-600" />;
        break;
      case "invalid":
        siteState = <ExclamationCircleIcon className="w-7 text-yellow-600" />;
        break;
      case "pending":
        siteState = (
          <svg
            class="animate-spin -ml-1 h-7 w-7 text-gray-600"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
        );
    }

    if (this.props.mode === "edit") {
      siteControls = (
        <div className="h-full flex items-center content-center mt-6">
          <button className="mr-2" onClick={() => this.props.onSiteUpdate(this.state)}>
            <CheckIcon className="w-5 text-gray-400 hover:text-green-600" />
          </button>
          <button onClick={() => this.props.onSiteCancel()}>
            <XIcon className="w-5 text-gray-400 hover:text-gray-600" />
          </button>
        </div>
      );
      siteState = <PencilIcon className="w-7 text-gray-600" />;
      siteDescription = (
        <div className="w-full flex">
          <div className="mr-2 w-36">
            <label
              for="email"
              className="block text-sm font-medium text-gray-700"
            >
              {__("Site Name")}
            </label>
            <div class="mt-1">
              <input
                type="text"
                name="name"
                className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                value={this.state.name}
                onChange={(e) =>
                  this.setState({
                    name: e.target.value,
                  })
                }
              />
            </div>
          </div>
          <div className="w-full mr-4">
            <label
              for="email"
              className="block text-sm font-medium text-gray-700"
            >
              {__("Site Url")}
            </label>
            <div class="mt-1">
              <input
                type="text"
                name="url"
                className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                value={this.state.url}
                onChange={(e) =>
                  this.setState({
                    url: e.target.value,
                  })
                }
              />
            </div>
          </div>
        </div>
      );
    } else {
      siteControls = (
        <div className="h-full flex items-center content-center">
          <button className="mr-2" onClick={() => this.props.onSiteEdit()}>
            <PencilIcon className="w-5 text-gray-400 hover:text-blue-600" />
          </button>
          <button onClick={() => this.props.onSiteDelete()}>
            <TrashIcon className="w-5 text-gray-400 hover:text-red-600" />
          </button>
        </div>
      );
      siteDescription = (
        <div className="w-full">
          <h3 className="text-lg">{this.props.name}</h3>
          <div className="text-gray-500">{this.props.url}</div>
        </div>
      );
    }

    return (
      <div className="flex items-center px-4 py-4 sm:px-6">
        <div className="h-full flex items-center content-center mr-3">
          {siteState}
        </div>
        {siteDescription}
        {siteControls}
      </div>
    );
  }
}
