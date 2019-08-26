import axios from "axios";

const apiClient = axios.create({
  withCredentials: false,
  headers: {
    Accept: "application/json",
    "Content-type": "application/json"
  },
  timeout: 10000
});

export default {
  deleteEvent(data) {
    return apiClient.get("/api/v1/event/" + data.id + "/delete", {
      headers: { Authorization: "Bearer " + data.token }
    });
  },
  eventSignUp(data) {
    return apiClient.post("/api/v1/event/sign-up", data, {
      headers: { Authorization: "Bearer " + data.token }
    });
  },
  getEvents(perPage, page, token) {
    return apiClient.get("/api/v1/events?page=" + page, {
      headers: { Authorization: "Bearer " + token }
    });
  },
  getEvent(id) {
    return apiClient.get("/api/v1/event/" + id);
  },
  getUser(id) {
    return apiClient.get("/api/v1/user/" + id);
  },
  login(user) {
    return apiClient.post("/api/login", user);
  },
  postEvent(event) {
    return apiClient.post("/api/v1/create", event, {
      headers: { Authorization: "Bearer " + event.token }
    });
  },
  userRegistration(registration) {
    return apiClient.post("/api/register/", registration);
  }
};
