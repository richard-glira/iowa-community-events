import EventService from "@/services/EventService.js";

export const namespaced = true;

export const state = {
  events: [],
  eventsTotal: 0,
  eventsDisplayed: 0,
  event: {},
  perPage: 4
};

export const mutations = {
  ADD_EVENT(state, event) {
    state.events.push(event);
  },
  ADD_EVENT_ATTENDEES(state, attendees) {
    state.event.attendees.push(attendees);
  },
  SET_EVENTS(state, events) {
    state.events = events;
  },
  SET_TOTAL_EVENTS(state, eventsTotal) {
    state.eventsTotal = eventsTotal;
  },
  SET_TOTAL_EVENTS_DISPLAYED(state) {
    state.eventsDisplayed += 1;
  },
  SET_EVENT(state, event) {
    state.event = event;
  },
  UPDATE_TOTAL_EVENTS(state, eventsTotal) {
    state.eventsTotal = eventsTotal;
  }
};

export const actions = {
  createEvent({ commit, dispatch }, event) {
    return EventService.postEvent(event)
      .then(response => {
        event.user_id = response.data.payload.event.user_id;
        event.attendees.push(response.data.payload.attendees);
        commit("ADD_EVENT", event);

        const notification = {
          type: "success",
          message: "Your event has been created!"
        };

        dispatch("notification/add", notification, { root: true });
      })
      .catch(error => {
        const notification = {
          type: "error",
          message: "There was a problem creating your event: " + error.message
        };
        dispatch("notification/add", notification, { root: true });
        throw error;
      });
  },
  fetchEvents({ commit, dispatch, state }, { page, token }) {
    return EventService.getEvents(state.perPage, page, token)
      .then(response => {
        commit("SET_EVENTS", response.data.payload.events.data);
        commit("SET_TOTAL_EVENTS", response.data.payload.events.total);
      })
      .catch(error => {
        const notification = {
          type: "error",
          message: "There was a problem fetching events: " + error.message
        };
        dispatch("notification/add", notification, { root: true });
      });
  },
  fetchEvent({ commit, getters }, id) {
    var event = getters.getEventById(id);

    if (event) {
      commit("SET_EVENT", event);
      return event;
    } else {
      return EventService.getEvent(id) // <--- Send the prop id to our EventService
        .then(response => {
          commit("SET_EVENT", response.data.payload.event);
          return response.data.payload.event;
        });
    }
  },
  setTotalEventsDisplayed({ commit }, increment) {
    commit("SET_TOTAL_EVENTS_DISPLAYED", increment);
  },
  signUp({ commit, dispatch }, data) {
    console.log(data);
    debugger;
    return EventService.eventSignUp(data)
      .then(response => {
        const notification = {
          type: "success",
          message: "Successfully signed up for event!"
        };

        commit("ADD_EVENT_ATTENDEES", response.data.payload.event);
        dispatch("notification/add", notification, { root: true });
      })
      .catch(error => {
        const notification = {
          type: "error",
          message: "There was a problem signing up for event: " + error.message
        };
        dispatch("notification/add", notification, { root: true });
        throw error;
      });
  }
};

export const getters = {
  getEventById: state => id => {
    return state.events.find(event => event.id === id);
  }
};
