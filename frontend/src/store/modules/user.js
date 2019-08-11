import EventService from "@/services/EventService.js";

export const namespaced = true;

export const actions = {
  fetchUser({ commit }) {
    return EventService.getUser().then(response => {
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
