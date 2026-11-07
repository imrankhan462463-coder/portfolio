/* ==========================================================================
   IMRAN — PORTFOLIO
   Live features: GitHub repositories, visitor location & Dhaka clock
   --------------------------------------------------------------------------
   01. Utilities
   02. Profile configuration
   03. Live GitHub section
   04. Live location (opt-in geolocation)
   05. Live Dhaka clock
   ========================================================================== */
(function () {
  "use strict";

  /* ======================================================================
     01. UTILITIES
     ====================================================================== */
  const $ = (selector, scope = document) => scope.querySelector(selector);
  const $$ = (selector, scope = document) => Array.from(scope.querySelectorAll(selector));
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  const DEFAULT_USERNAME = "imrankhan462463-coder";
  const DEFAULT_LOCATION = "Mirpur 10, Dhaka, Bangladesh";
  const DEFAULT_TIMEZONE = "Asia/Dhaka";
  const GITHUB_CACHE_TTL = 10 * 60 * 1000;

  const LANGUAGE_COLORS = {
    JavaScript: "#f1e05a",
    TypeScript: "#3178c6",
    HTML: "#e34c26",
    CSS: "#563d7c",
    SCSS: "#c6538c",
    PHP: "#4f5d95",
    Python: "#3572a5",
    Java: "#b07219",
    "C#": "#178600",
    "C++": "#f34b7d",
    C: "#555555",
    Go: "#00add8",
    Rust: "#dea584",
    Ruby: "#701516",
    Shell: "#89e051",
    Vue: "#41b883",
    Svelte: "#ff3e00",
    Dart: "#00b4ab",
    Kotlin: "#a97bff",
    Swift: "#f05138",
    SQL: "#e38c00",
    Dockerfile: "#384d54",
  };

  const createEl = (tag, className, text) => {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (typeof text === "string") node.textContent = text;
    return node;
  };

  const createIcon = (name, className) => {
    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    if (className) svg.setAttribute("class", className);
    svg.setAttribute("aria-hidden", "true");
    const use = document.createElementNS("http://www.w3.org/2000/svg", "use");
    use.setAttribute("href", "#icon-" + name);
    svg.appendChild(use);
    return svg;
  };

  const setText = (node, value) => {
    if (node && typeof value === "string" && value.trim() !== "") node.textContent = value;
  };

  const fetchJSON = async (url, timeout = 9000) => {
    const controller = new AbortController();
    const timer = window.setTimeout(() => controller.abort(), timeout);

    try {
      const response = await fetch(url, {
        cache: "no-store",
        credentials: "omit",
        referrerPolicy: "no-referrer",
        headers: { Accept: "application/json" },
        signal: controller.signal,
      });

      if (!response.ok) throw new Error("HTTP " + response.status);

      const contentType = response.headers.get("content-type") || "";
      if (contentType.indexOf("json") === -1) throw new Error("Unexpected response type");

      return await response.json();
    } finally {
      window.clearTimeout(timer);
    }
  };

  const relativeTime = (value) => {
    if (!value) return "";

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "";

    const seconds = (date.getTime() - Date.now()) / 1000;
    const absolute = Math.abs(seconds);

    if (absolute < 60) return "just now";

    const units = [
      ["year", 31536000],
      ["month", 2592000],
      ["week", 604800],
      ["day", 86400],
      ["hour", 3600],
      ["minute", 60],
    ];

    const formatter =
      typeof Intl !== "undefined" && Intl.RelativeTimeFormat
        ? new Intl.RelativeTimeFormat("en", { numeric: "auto" })
        : null;

    for (let index = 0; index < units.length; index += 1) {
      const unit = units[index][0];
      const size = units[index][1];
      if (absolute >= size) {
        const amount = Math.round(seconds / size);
        return formatter ? formatter.format(amount, unit) : `${Math.abs(amount)} ${unit}s ago`;
      }
    }

    return "just now";
  };

  const mapsSearchUrl = (query) =>
    "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(query || "");

  const openStreetMapUrl = (latitude, longitude) =>
    `https://www.openstreetmap.org/?mlat=${latitude}&mlon=${longitude}#map=15/${latitude}/${longitude}`;

  /* ======================================================================
     02. PROFILE CONFIGURATION
     ====================================================================== */
  const livePanel = $("[data-live-panel]");
  const githubSection = $("[data-github-section]");

  const config = {
    username:
      (githubSection && githubSection.getAttribute("data-github-username")) || DEFAULT_USERNAME,
    location:
      (livePanel && livePanel.getAttribute("data-fallback-location")) || DEFAULT_LOCATION,
    timezone: (livePanel && livePanel.getAttribute("data-timezone")) || DEFAULT_TIMEZONE,
    githubUrl: "",
  };

  const profileUsername = (profile) => {
    if (profile && typeof profile.github_username === "string" && profile.github_username.trim()) {
      return profile.github_username.trim().replace(/^@/, "");
    }

    if (profile && typeof profile.github === "string") {
      const match = profile.github.match(/github\.com\/([^/?#]+)/i);
      if (match) return match[1];
    }

    return "";
  };

  let booted = false;
  let githubLoading = false;

  const applyProfileConfig = (profile) => {
    if (!profile) return;

    const previousUsername = config.username;
    const username = profileUsername(profile);
    if (username) config.username = username;

    if (typeof profile.location === "string" && profile.location.trim()) {
      config.location = profile.location.trim();
    }

    if (typeof profile.location_timezone === "string" && profile.location_timezone.trim()) {
      config.timezone = profile.location_timezone.trim();
    }

    if (livePanel) {
      livePanel.setAttribute("data-fallback-location", config.location);
      livePanel.setAttribute("data-timezone", config.timezone);
    }

    if (githubLoading) return;

    if (booted && config.username !== previousUsername) {
      loadGitHub();
    }
  };

  /* ======================================================================
     03. LIVE GITHUB SECTION
     ====================================================================== */
  const githubReposEl = githubSection ? $("[data-github-repos]", githubSection) : null;
  const githubStatusEl = githubSection ? $("[data-github-status]", githubSection) : null;
  const githubCacheKey = () => "imran-github:" + config.username.toLowerCase();

  const setGithubStatus = (message, type) => {
    if (!githubStatusEl) return;
    githubStatusEl.textContent = message;
    githubStatusEl.classList.remove("is-error", "is-loading", "is-success");
    if (type) githubStatusEl.classList.add("is-" + type);
  };

  const setGithubBusy = (busy) => {
    if (!githubReposEl) return;
    githubReposEl.setAttribute("aria-busy", busy ? "true" : "false");
  };

  const readGithubCache = () => {
    try {
      const raw = window.sessionStorage.getItem(githubCacheKey());
      if (!raw) return null;

      const cached = JSON.parse(raw);
      if (
        cached &&
        cached.savedAt &&
        Date.now() - cached.savedAt < GITHUB_CACHE_TTL &&
        cached.data &&
        cached.data.profile &&
        Array.isArray(cached.data.repos)
      ) {
        return cached.data;
      }
    } catch (error) {
      // Storage can be unavailable in private modes — ignore it.
    }

    return null;
  };

  const writeGithubCache = (data) => {
    try {
      window.sessionStorage.setItem(
        githubCacheKey(),
        JSON.stringify({ savedAt: Date.now(), data: data })
      );
    } catch (error) {
      // Cache is optional.
    }
  };

  const normaliseGithub = (profile, repos) => {
    const safeProfile = profile && typeof profile === "object" ? profile : {};
    const list = Array.isArray(repos) ? repos : [];

    return {
      ok: true,
      source: "live",
      username: safeProfile.login || config.username,
      profile: {
        login: String(safeProfile.login || config.username),
        name: String(safeProfile.name || ""),
        avatar_url: String(safeProfile.avatar_url || ""),
        html_url: String(safeProfile.html_url || "https://github.com/" + config.username),
        bio: String(safeProfile.bio || ""),
        public_repos: Number(safeProfile.public_repos || 0),
        followers: Number(safeProfile.followers || 0),
        following: Number(safeProfile.following || 0),
        created_at: String(safeProfile.created_at || ""),
      },
      repos: list
        .filter((repo) => repo && typeof repo === "object" && repo.name)
        .map((repo) => ({
          name: String(repo.name),
          description: String(repo.description || ""),
          html_url: String(repo.html_url || ""),
          language: String(repo.language || ""),
          stargazers_count: Number(repo.stargazers_count || 0),
          forks_count: Number(repo.forks_count || 0),
          topics: Array.isArray(repo.topics) ? repo.topics.slice(0, 4).map(String) : [],
          updated_at: String(repo.updated_at || ""),
          pushed_at: String(repo.pushed_at || ""),
          fork: Boolean(repo.fork),
          archived: Boolean(repo.archived),
          homepage: String(repo.homepage || ""),
        })),
    };
  };

  const renderGithubProfile = (data) => {
    if (!githubSection || !data) return;

    const profile = data.profile || {};
    const login = profile.login || data.username || config.username;
    const githubUrl = profile.html_url || "https://github.com/" + login;

    const avatar = $("[data-github-avatar]", githubSection);
    if (avatar && profile.avatar_url) {
      avatar.src = profile.avatar_url;
      avatar.alt = (profile.name || login) + " on GitHub";
      avatar.addEventListener(
        "error",
        () => {
          avatar.src = "assets/images/profile.svg";
        },
        { once: true }
      );
    }

    setText($("[data-github-handle]", githubSection), "@" + login);
    setText($("[data-github-name]", githubSection), profile.name || login);
    setText(
      $("[data-github-bio]", githubSection),
      profile.bio || "Web designer & WordPress developer building modern experiences in public."
    );
    setText($("[data-github-repos-count]", githubSection), String(profile.public_repos || 0));
    setText($("[data-github-followers]", githubSection), String(profile.followers || 0));
    setText($("[data-github-following]", githubSection), String(profile.following || 0));

    if (profile.created_at) {
      const created = new Date(profile.created_at);
      if (!Number.isNaN(created.getTime())) {
        setText($("[data-github-since]", githubSection), String(created.getFullYear()));
      }
    }

    $$("[data-profile-link='github']", githubSection).forEach((link) => {
      link.setAttribute("href", githubUrl);
      link.setAttribute("target", "_blank");
      link.setAttribute("rel", "noopener noreferrer");
    });

    const allLink = $("[data-github-all]", githubSection);
    if (allLink) allLink.setAttribute("href", githubUrl + "?tab=repositories");
  };

  const githubRepoCard = (repo) => {
    const card = createEl("a", "gh-repo");
    card.href = repo.html_url || "https://github.com/" + config.username;
    card.target = "_blank";
    card.rel = "noopener noreferrer";

    const head = createEl("div", "gh-repo__head");
    const icon = createEl("span", "gh-repo__icon");
    icon.appendChild(createIcon("repo"));
    head.appendChild(icon);
    head.appendChild(createEl("span", "gh-repo__name", repo.name));
    const arrow = createEl("span", "gh-repo__arrow");
    arrow.appendChild(createIcon("arrow-up-right"));
    head.appendChild(arrow);
    card.appendChild(head);

    card.appendChild(
      createEl(
        "p",
        "gh-repo__desc",
        repo.description || "No description provided yet — open the repository to explore the code."
      )
    );

    if (repo.topics && repo.topics.length) {
      const tags = createEl("ul", "gh-repo__tags");
      tags.setAttribute("aria-label", "Repository topics");
      repo.topics.slice(0, 3).forEach((topic) => {
        tags.appendChild(createEl("li", "", String(topic)));
      });
      card.appendChild(tags);
    }

    const foot = createEl("div", "gh-repo__foot");
    const meta = createEl("span", "gh-repo__meta");

    if (repo.language) {
      const language = createEl("span", "gh-repo__lang");
      const dot = createEl("i", "gh-repo__dot");
      dot.style.setProperty("--lang-color", LANGUAGE_COLORS[repo.language] || "#8b5cf6");
      language.appendChild(dot);
      language.appendChild(document.createTextNode(repo.language));
      meta.appendChild(language);
    }

    if (repo.stargazers_count > 0) {
      const stars = createEl("span", "gh-repo__stat");
      stars.appendChild(createIcon("star"));
      stars.appendChild(document.createTextNode(String(repo.stargazers_count)));
      stars.setAttribute("aria-label", repo.stargazers_count + " stars");
      meta.appendChild(stars);
    }

    if (repo.forks_count > 0) {
      const forks = createEl("span", "gh-repo__stat");
      forks.appendChild(createIcon("fork"));
      forks.appendChild(document.createTextNode(String(repo.forks_count)));
      forks.setAttribute("aria-label", repo.forks_count + " forks");
      meta.appendChild(forks);
    }

    if (repo.fork) meta.appendChild(createEl("span", "gh-repo__flag", "Fork"));
    if (repo.archived) meta.appendChild(createEl("span", "gh-repo__flag", "Archived"));

    foot.appendChild(meta);

    const updated = relativeTime(repo.pushed_at || repo.updated_at);
    if (updated) {
      const updatedEl = createEl("span", "gh-repo__updated", "Updated " + updated);
      foot.appendChild(updatedEl);
    }

    card.appendChild(foot);
    return card;
  };

  const renderGithubError = (message) => {
    if (!githubReposEl) return;

    githubReposEl.textContent = "";
    const card = createEl("div", "gh-repo gh-repo--error");
    const icon = createEl("span", "gh-repo__icon");
    icon.appendChild(createIcon("github"));
    card.appendChild(icon);
    card.appendChild(createEl("h4", "", "GitHub is taking a short break"));
    card.appendChild(createEl("p", "gh-repo__desc", message));

    const link = createEl("a", "btn btn--ghost btn--sm");
    link.href = config.githubUrl || "https://github.com/" + config.username;
    link.target = "_blank";
    link.rel = "noopener noreferrer";
    const label = createEl("span", "", "Open my GitHub");
    link.appendChild(label);
    link.appendChild(createIcon("arrow-up-right", "btn__icon"));
    card.appendChild(link);

    githubReposEl.appendChild(card);
  };

  const renderGithubRepos = (data) => {
    if (!githubReposEl) return;

    const repos = (data.repos || [])
      .slice()
      .sort((a, b) => {
        const aTime = new Date(a.pushed_at || a.updated_at || 0).getTime();
        const bTime = new Date(b.pushed_at || b.updated_at || 0).getTime();
        return bTime - aTime;
      })
      .slice(0, 6);

    githubReposEl.textContent = "";

    if (!repos.length) {
      const empty = createEl("div", "gh-repo gh-repo--error");
      empty.appendChild(createEl("h4", "", "No public repositories yet"));
      empty.appendChild(
        createEl("p", "gh-repo__desc", "New work is on the way. Follow my GitHub to see it first.")
      );
      githubReposEl.appendChild(empty);
      setGithubStatus("The profile is connected, but there are no public repositories to show yet.");
      return;
    }

    repos.forEach((repo) => githubReposEl.appendChild(githubRepoCard(repo)));

    const countEl = $("[data-github-count]", githubSection);
    if (countEl) countEl.textContent = repos.length + (repos.length === 1 ? " repo" : " repos");

    const sourceMessages = {
      live: "Live from GitHub.",
      cache: "Cached for a few minutes to stay gentle on the GitHub API.",
      stale: "Showing recent cached data while GitHub is unavailable.",
    };

    setGithubStatus(
      "Showing " +
        repos.length +
        " public " +
        (repos.length === 1 ? "repository" : "repositories") +
        ". " +
        (sourceMessages[data.source] || sourceMessages.live),
      "success"
    );
  };

  const loadGitHub = async () => {
    if (!githubSection || !githubReposEl || githubLoading) return;

    githubLoading = true;
    setGithubBusy(true);
    setGithubStatus("Syncing public repositories from GitHub…", "loading");

    let data = null;

    // Preferred path: the cached PHP endpoint. Falls back silently on static hosting.
    if (window.location.protocol === "http:" || window.location.protocol === "https:") {
      try {
        const endpoint = "github.php?user=" + encodeURIComponent(config.username);
        data = await fetchJSON(endpoint, 12000);
      } catch (error) {
        data = null;
      }
    }

    if (!data || data.ok !== true) {
      const cached = readGithubCache();
      if (cached) {
        data = cached;
      } else {
        try {
          const encoded = encodeURIComponent(config.username);
          const [profile, repos] = await Promise.all([
            fetchJSON("https://api.github.com/users/" + encoded),
            fetchJSON(
              "https://api.github.com/users/" +
                encoded +
                "/repos?per_page=100&sort=updated&type=owner"
            ),
          ]);
          data = normaliseGithub(profile, repos);
          writeGithubCache(data);
        } catch (error) {
          const rateLimited =
            error && String(error.message || "").indexOf("403") !== -1
              ? "GitHub’s public API rate limit was reached."
              : "Live repository data could not be loaded right now.";
          setGithubStatus(rateLimited + " You can still browse everything on GitHub.", "error");
          renderGithubError(
            rateLimited + " The profile link below always works, and the section will retry on your next visit."
          );
          setGithubBusy(false);
          githubLoading = false;
          return;
        }
      }
    }

    if (!data || !data.profile) {
      setGithubStatus("GitHub data came back incomplete. Browse the full profile directly.", "error");
      renderGithubError("The repository list could not be read. Everything is still available on GitHub.");
      setGithubBusy(false);
      githubLoading = false;
      return;
    }

    config.githubUrl = data.profile.html_url || "https://github.com/" + config.username;
    renderGithubProfile(data);
    renderGithubRepos(data);
    setGithubBusy(false);
    githubLoading = false;
  };

  /* ======================================================================
     04. LIVE LOCATION (OPT-IN GEOLOCATION)
     ====================================================================== */
  const locationValueEl = livePanel ? $("[data-location-value]", livePanel) : null;
  const locationMetaEl = livePanel ? $("[data-location-meta]", livePanel) : null;
  const locationStatusEl = livePanel ? $("[data-location-status]", livePanel) : null;
  const locationButton = livePanel ? $("[data-location-detect]", livePanel) : null;
  const locationButtonLabel = livePanel ? $("[data-location-button-label]", livePanel) : null;
  const locationReset = livePanel ? $("[data-location-reset]", livePanel) : null;
  const locationMap = livePanel ? $("[data-location-map]", livePanel) : null;

  let liveLocation = null;
  let locationRequestInFlight = false;

  const setLocationStatus = (label) => {
    if (locationStatusEl) locationStatusEl.textContent = label;
  };

  const setLocationButtonLoading = (loading) => {
    if (locationButton) {
      locationButton.disabled = loading;
      locationButton.classList.toggle("is-loading", loading);
      locationButton.setAttribute("aria-busy", loading ? "true" : "false");
    }

    if (locationButtonLabel) {
      if (loading) {
        locationButtonLabel.textContent = "Locating…";
      } else {
        locationButtonLabel.textContent = liveLocation ? "Update location" : "Use my location";
      }
    }
  };

  const showSavedLocation = (message) => {
    liveLocation = null;
    setLocationStatus("Saved location");
    setText(locationValueEl, config.location);
    setText(
      locationMetaEl,
      message || "My base in Dhaka. Detect your area to see where this page is being viewed from."
    );

    if (locationMap) {
      locationMap.hidden = false;
      locationMap.setAttribute("href", mapsSearchUrl(config.location));
    }

    if (locationReset) locationReset.hidden = true;
    if (livePanel) livePanel.setAttribute("data-location-state", "saved");
    setLocationButtonLoading(false);
  };

  const showLiveLocation = (place, latitude, longitude, accuracy) => {
    const accuracyText =
      typeof accuracy === "number" && Number.isFinite(accuracy)
        ? "±" + Math.round(accuracy) + " m"
        : "approximate";

    liveLocation = { place: place, latitude: latitude, longitude: longitude, accuracy: accuracy };

    setLocationStatus("Your live location");
    setText(locationValueEl, place);
    setText(
      locationMetaEl,
      "Approximate area · " + accuracyText + " · updated " + relativeTime(new Date().toISOString())
    );

    if (locationMap) {
      locationMap.hidden = false;
      locationMap.setAttribute("href", openStreetMapUrl(latitude, longitude));
    }

    if (locationReset) locationReset.hidden = false;
    if (livePanel) livePanel.setAttribute("data-location-state", "live");
    setLocationButtonLoading(false);
  };

  const reverseGeocode = async (latitude, longitude) => {
    const url =
      "https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=" +
      encodeURIComponent(latitude) +
      "&longitude=" +
      encodeURIComponent(longitude) +
      "&localityLanguage=en";

    const data = await fetchJSON(url, 7000);
    if (!data || typeof data !== "object") return "";

    const locality = data.locality || data.city || data.principalSubdivision || "";
    const secondary =
      data.city && data.city !== locality
        ? data.city
        : data.principalSubdivision && data.principalSubdivision !== locality
          ? data.principalSubdivision
          : "";
    const country = data.countryName || "";

    const parts = [];
    [locality, secondary, country].forEach((part) => {
      if (
        part &&
        !parts.some((existing) => existing.toLowerCase() === String(part).toLowerCase())
      ) {
        parts.push(String(part));
      }
    });

    return parts.join(", ");
  };

  const formatCoordinates = (latitude, longitude) => {
    const lat = Math.abs(latitude).toFixed(4) + "° " + (latitude >= 0 ? "N" : "S");
    const lng = Math.abs(longitude).toFixed(4) + "° " + (longitude >= 0 ? "E" : "W");
    return lat + ", " + lng;
  };

  const locationFailure = (message, denied) => {
    if (liveLocation) {
      // Keep the last successful result visible if a refresh fails.
      setLocationStatus(denied ? "Permission denied" : "Using last result");
      setText(locationMetaEl, message + " Showing your last detected area.");
      setLocationButtonLoading(false);
      return;
    }

    showSavedLocation(message);
    setLocationStatus(denied ? "Permission denied" : "Saved location");
  };

  const requestLocation = () => {
    if (!locationButton || locationRequestInFlight) return;

    if (!("geolocation" in navigator)) {
      showSavedLocation(
        "This browser doesn’t support geolocation. Showing my saved base in Dhaka instead."
      );
      locationButton.disabled = true;
      return;
    }

    locationRequestInFlight = true;
    setLocationButtonLoading(true);
    setLocationStatus("Requesting permission…");
    setText(
      locationMetaEl,
      "Your browser may ask for permission. This site does not store your coordinates."
    );

    navigator.geolocation.getCurrentPosition(
      async (position) => {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        const accuracy = position.coords.accuracy;
        let place = "";

        try {
          place = await reverseGeocode(latitude, longitude);
        } catch (error) {
          place = "";
        }

        locationRequestInFlight = false;
        showLiveLocation(
          place || formatCoordinates(latitude, longitude),
          latitude,
          longitude,
          accuracy
        );
      },
      (error) => {
        locationRequestInFlight = false;

        if (error && error.code === 1) {
          locationFailure(
            "Location permission was not granted. Showing my saved base in Dhaka instead.",
            true
          );
        } else if (error && error.code === 3) {
          locationFailure("The location request timed out. Showing my saved base in Dhaka instead.", false);
        } else {
          locationFailure(
            "Your location is unavailable right now. Showing my saved base in Dhaka instead.",
            false
          );
        }
      },
      { enableHighAccuracy: false, timeout: 10000, maximumAge: 300000 }
    );
  };

  /* ======================================================================
     05. LIVE DHAKA CLOCK
     ====================================================================== */
  const clockTimeEl = livePanel ? $("[data-clock-time]", livePanel) : null;
  const clockSecondsEl = livePanel ? $("[data-clock-seconds]", livePanel) : null;
  const clockMeridiemEl = livePanel ? $("[data-clock-meridiem]", livePanel) : null;
  const clockDateEl = livePanel ? $("[data-clock-date]", livePanel) : null;
  const clockProgressEl = livePanel ? $("[data-clock-progress]", livePanel) : null;
  const clockOffsetEl = livePanel ? $("[data-clock-offset]", livePanel) : null;

  let clockTimer = null;

  const buildClock = () => {
    let timeFormatter;
    let dateFormatter;
    let offsetFormatter;

    try {
      timeFormatter = new Intl.DateTimeFormat("en-US", {
        timeZone: config.timezone,
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
      });
      dateFormatter = new Intl.DateTimeFormat("en-GB", {
        timeZone: config.timezone,
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
      });
    } catch (error) {
      // Invalid timezone in settings — fall back to the portfolio default.
      if (config.timezone !== DEFAULT_TIMEZONE) {
        config.timezone = DEFAULT_TIMEZONE;
        return buildClock();
      }
    }

    try {
      offsetFormatter = new Intl.DateTimeFormat("en-US", {
        timeZone: config.timezone,
        timeZoneName: "shortOffset",
      });
    } catch (error) {
      offsetFormatter = null;
    }

    const tick = () => {
      const now = new Date();

      const timeParts = {};
      timeFormatter.formatToParts(now).forEach((part) => {
        if (part.type !== "literal") timeParts[part.type] = part.value;
      });

      const dateParts = {};
      dateFormatter.formatToParts(now).forEach((part) => {
        if (part.type !== "literal") dateParts[part.type] = part.value;
      });

      if (clockTimeEl) {
        clockTimeEl.textContent = (timeParts.hour || "--") + ":" + (timeParts.minute || "--");
      }
      if (clockSecondsEl) clockSecondsEl.textContent = timeParts.second || "--";
      if (clockMeridiemEl) {
        clockMeridiemEl.textContent = (timeParts.dayPeriod || "AM").toUpperCase();
      }
      if (clockDateEl) {
        clockDateEl.textContent =
          (dateParts.weekday || "") +
          ", " +
          (dateParts.day || "") +
          " " +
          (dateParts.month || "") +
          " " +
          (dateParts.year || "");
      }
      if (clockProgressEl) {
        const second = Number(timeParts.second || 0);
        if (second === 0) {
          // Jump back to the start instantly instead of animating backwards.
          clockProgressEl.style.transition = "none";
          clockProgressEl.style.transform = "scaleX(0)";
          window.requestAnimationFrame(() => {
            clockProgressEl.style.transition = "";
          });
        } else {
          clockProgressEl.style.transform = "scaleX(" + second / 60 + ")";
        }
      }
      if (clockOffsetEl && offsetFormatter) {
        const offsetPart = offsetFormatter
          .formatToParts(now)
          .find((part) => part.type === "timeZoneName");
        if (offsetPart && offsetPart.value) clockOffsetEl.textContent = offsetPart.value;
      }

      const clockRegion = clockTimeEl ? clockTimeEl.closest(".live-clock") : null;
      if (clockRegion && timeParts.hour) {
        clockRegion.setAttribute(
          "aria-label",
          "Current local time in Dhaka, Bangladesh: " +
            timeParts.hour +
            ":" +
            timeParts.minute +
            " " +
            timeParts.dayPeriod
        );
      }
    };

    const schedule = () => {
      if (clockTimer) window.clearTimeout(clockTimer);
      tick();
      // Align updates to the actual second boundary so the clock never drifts.
      clockTimer = window.setTimeout(schedule, 1000 - (Date.now() % 1000));
    };

    const start = () => {
      if (clockTimer) window.clearTimeout(clockTimer);
      schedule();
    };

    document.addEventListener("visibilitychange", () => {
      if (document.hidden) {
        if (clockTimer) window.clearTimeout(clockTimer);
        clockTimer = null;
      } else {
        start();
      }
    });

    start();
  };

  /* ======================================================================
     BOOT
     ====================================================================== */
  const initLocation = () => {
    if (!livePanel || !locationButton) return;

    showSavedLocation();
    locationButton.addEventListener("click", requestLocation);

    if (locationReset) {
      locationReset.addEventListener("click", () => {
        showSavedLocation();
      });
    }

    if ("permissions" in navigator && typeof navigator.permissions.query === "function") {
      navigator.permissions
        .query({ name: "geolocation" })
        .then((permission) => {
          if (permission.state === "granted") {
            // The visitor already allowed geolocation for this site — safe to show it.
            requestLocation();
          } else if (permission.state === "denied") {
            setLocationStatus("Permission blocked");
            setText(
              locationMetaEl,
              "Location permission is blocked in your browser. Showing my saved base in Dhaka — enable location in site settings to see your area."
            );
          }

          permission.onchange = () => {
            if (permission.state === "granted" && !liveLocation) requestLocation();
          };
        })
        .catch(() => {
          // The Permissions API is optional; the button still works without it.
        });
    }
  };

  const boot = () => {
    if (booted) return;
    booted = true;

    buildClock();
    initLocation();
    loadGitHub();
  };

  document.addEventListener(
    "portfolio:profile-ready",
    (event) => {
      applyProfileConfig(event.detail);
      boot();
    },
    { once: true }
  );

  if (window.portfolioProfile) {
    applyProfileConfig(window.portfolioProfile);
    boot();
  } else {
    // The profile file is always local. This short timeout only exists so a
    // slow request can never block the clock or the location feature.
    window.setTimeout(() => {
      if (booted) return;
      applyProfileConfig(window.portfolioProfile || null);
      boot();
    }, 700);
  }

  // Respect users who ask their system to reduce motion.
  if (reduceMotion.matches && clockProgressEl) {
    clockProgressEl.style.transition = "none";
  }
})();
