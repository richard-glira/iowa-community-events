import EventService from "@/services/EventService.js";

export const namespaced = true;

export const actions = {
  fetchUser({ commit }, userId = 1) {
    return EventService.getUser(userId).then(response => {
      commit("SET_USER", response.data.payload.user);
    });
  }
};

export const mutations = {
  SET_USER(state, user) {
    state.user = user;
  }
};

export const state = {
  user: {}
};
