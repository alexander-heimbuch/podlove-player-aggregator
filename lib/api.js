const headers = {
  "Content-Type": "application/json",
  "X-WP-Nonce": window.PODLOVE_PLAYER_AGGREGATOR.nonce,
};

export const get = (api, params = {}) => {
  const query = new URLSearchParams(params);
  return fetch(
    [window.PODLOVE_PLAYER_AGGREGATOR.api[api]].join("/") +
      (query ? "?" + query : ""),
    {
      method: "GET",
      headers
    }
  )
    .then((response) => {
      if (!response.ok) {
        throw Error(response);
      }

      return response;
    })
    .then((response) => response.json());
};

export const post = (api, data = {}) =>
  fetch([window.PODLOVE_PLAYER_AGGREGATOR.api[api]].join("/"), {
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
