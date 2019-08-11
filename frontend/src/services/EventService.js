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
  getEvents(perPage, page) {
    return apiClient.get("/api/v1/events?page=" + page);
  },
  getEvent(id) {
    return apiClient.get("/api/v1/event/" + id);
  },
  postEvent(event) {
    console.log(event);
    return apiClient.post("/api/v1/create", event);
  },
  getUser(id = 1) {
    return apiClient.get("/api/v1/user/" + id);
  }
};
