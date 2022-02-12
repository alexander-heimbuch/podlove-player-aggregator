const headers = {
  "Content-Type": "application/json",
  "X-WP-Nonce": window.PODLOVE_PLAYER_AGGREGATOR.nonce,
};

export const get = (api, params = []) =>
  fetch([window.PODLOVE_PLAYER_AGGREGATOR.api[api], ...params].join("/"), {
    method: "GET",
    headers,
  })
    .then((response) => {
      if (!response.ok) {
        throw Error(response);
      }

      return response;
    })
    .then((response) => response.json());

export const post = (api, params = [], data = {}) =>
  fetch([window.PODLOVE_PLAYER_AGGREGATOR.api[api], ...params].join("/"), {
    method: "POST",
    headers,
    body: JSON.stringify(data),
  })
    .then((response) => {
      if (!response.ok) {
        throw Error(response);
      }

      return response;
    })
    .then((response) => response.json());
