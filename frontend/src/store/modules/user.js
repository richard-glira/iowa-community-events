import EventService from "@/services/EventService.js";

export const namespaced = true;

export const actions = {
  fetchUser({ commit }, userId) {
    return EventService.getUser(userId).then(response => {
      commit("SET_USER", response.data.payload.user);
    });
  },
  login({ commit, dispatch }, user) {
    return EventService.login(user)
      .then(response => {
        commit("SET_USER", response.data.payload.user);
        commit("SET_AUTH_TOKEN", response.data.payload.token);
        commit("SET_USER_LOGIN_STATUS", true);
      })
      .catch(error => {
        const notification = {
          type: "error",
          message: "Invalid Credentials"
        };

        dispatch("notification/add", notification, { root: true });
        throw error;
      });
  },
  logout({ commit, dispatch }, user) {
    return EventService.logout(user)
      .then(() => {
        const notification = {
          type: "success",
          message: "Successfully logged out!"
        };

        dispatch("notification/add", notification, { root: true });
        commit("CLEAR_AUTH_TOKEN");
        commit("CLEAR_USER");
        commit("SET_USER_LOGIN_STATUS", false);
      })
      .catch(error => {
        const notification = {
          type: "error",
          message: "Error logging out, email richardlr23@gmail.com for support."
        };

        dispatch("notification/add", notification, { root: true });
        throw error;
      });
  },
  userLoginStatus({ commit }, status) {
    commit("SET_USER_LOGIN_STATUS", status);
  },
  userRegistration({ commit }, registration) {
    return EventService.userRegistration(registration).then(response => {
      commit("SET_AUTH_TOKEN", response.data.payload.token);
    });
  }
};

export const mutations = {
  CLEAR_AUTH_TOKEN(state) {
    state.token = "";
  },
  CLEAR_USER() {
    state.user = {};
  },
  SET_AUTH_TOKEN(state, token) {
    state.token = token;
  },
  SET_USER(state, user) {
    state.user = user;
  },
  SET_USER_LOGIN_STATUS(state, status) {
    state.isLoggedIn = status;
  }
};

export const state = {
  token: "",
  isLoggedIn: false,
  user: {}
};
