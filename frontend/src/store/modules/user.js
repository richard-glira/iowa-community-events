import EventService from "@/services/EventService.js";

export const namespaced = true;

export const actions = {
  fetchUser({ commit }, userId) {
    return EventService.getUser(userId).then(response => {
      commit("SET_USER", response.data.payload.user);
    });
  },
  login({ commit }, user) {
    return EventService.login(user).then(response => {
      commit("SET_USER", response.data.payload.user);
      commit("SET_AUTH_TOKEN", response.data.payload.token);
      commit("SET_USER_LOGIN_STATUS", true);
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
