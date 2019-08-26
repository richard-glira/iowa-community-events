<template>
    <v-layout>
        <v-flex xs12>
            <v-card class="mx-auto mt-5">
                <v-container grid-list-md text-xs-center>
                    <v-layout row wrap>
                        <v-flex xs10>
                            <v-card-title class="px-3">
                                <v-toolbar color="grey darken-2" dark>
                                    <v-toolbar-title class="title">
                                        <v-tooltip top>
                                            <template v-slot:activator="{ on }">
                                                <span v-on="on" class="event-info">
                                                    <v-icon>spa</v-icon> 
                                                    <span class="ml-1"><b>{{ title }}</b></span>
                                                </span><br>
                                            </template>
                                            <span>Event</span>
                                        </v-tooltip>
                                    </v-toolbar-title>
                                    <v-spacer></v-spacer>
                                </v-toolbar>
                            </v-card-title>
                        </v-flex>
                        <v-flex xs2>
                            <v-card-title>
                                <v-tooltip bottom>
                                    <template v-slot:activator="{ on }">
                                        <v-btn
                                            fab
                                            outline 
                                            color="teal" 
                                            :href="`${emailOrganizer}`"
                                            v-on="on"
                                        >
                                            <v-icon>email</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Contact Organizer</span>
                                </v-tooltip>
                            </v-card-title>
                        </v-flex>
                    </v-layout>
                    <v-layout class="px-4" row wrap>
                        <v-flex xs6>
                            <v-card>
                                <v-card-text class="px-0">
                                    <v-tooltip right>
                                        <template v-slot:activator="{ on }">
                                            <span v-on="on" class="event-info">
                                                <v-icon class="mr-1">category</v-icon> <b>Category</b><br/> 
                                                <span class="ml-4">{{ category }}</span>
                                                </span>
                                        </template>
                                        <span>Category</span>
                                    </v-tooltip>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                        <v-flex xs6>
                            <v-card>
                                <v-card-text class="px-0">
                                    <v-tooltip right>
                                        <template v-slot:activator="{ on }">
                                            <span v-on="on" class="date-info event-info">
                                                <v-icon>access_alarm</v-icon> Time<br/> 
                                                <span class="ml-4">{{ time }} on {{ eventDate | date }}</span>
                                            </span>
                                        </template>
                                        <span>Date & Time</span>
                                    </v-tooltip>
                                </v-card-text>
                            </v-card>
                        </v-flex> 
                        <v-flex xs9>
                            <v-card>
                                <v-card-text class="px-0">
                                    <v-tooltip right>
                                        <template v-slot:activator="{ on }">
                                            <span v-on="on" class="event-info"><v-icon>place</v-icon> {{ eventLocation }}</span><br/> 
                                        </template>
                                        <span>Location</span>
                                    </v-tooltip>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                        <v-flex xs3>
                            <v-card>
                                <v-card-text class="px-0">
                                    <v-tooltip right>
                                        <template v-slot:activator="{ on }">
                                            <span v-on="on" class="date-info event-info"><v-icon>map</v-icon> Directions</span><br/> 
                                        </template>
                                        <span>View in maps</span>
                                    </v-tooltip>
                                </v-card-text>
                            </v-card>
                        </v-flex> 
                        <v-flex xs12>
                            <v-card>
                                <v-card-text class="px-0">
                                    <v-tooltip right>
                                        <template v-slot:activator="{ on }">
                                            <span v-on="on" class="event-info pl-2 pr-2">
                                                <span class="pb-2" style="padding-right: 10px;">
                                                    <v-icon>info</v-icon> Event Details<br/>
                                                </span>
                                                <span class="pl-3">{{ eventDescription }}</span>
                                            </span>
                                        </template>
                                        <span>Description</span>
                                    </v-tooltip>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-container>
                <v-card-actions>
                    <v-btn 
                        flat
                        color="success"
                        @click="eventSignUp"
                        v-if="!isUserRegistered"
                    >
                        <v-icon>person_add</v-icon> Sign me up
                    </v-btn>
                    <div class="text-xs-center">
                        <v-chip 
                            dark 
                            outline 
                            color="success"
                            v-if="isUserRegistered"
                        >
                            <v-icon class="font-success">done</v-icon> 
                            <span class="font-success">Registered for this event</span>
                        </v-chip>
                    </div>
                    <v-btn 
                        flat 
                        color="error"
                        v-if="isEventOrganizer"
                    >
                        <v-icon>delete_forever</v-icon> Discard Event
                    </v-btn>
                    <v-tooltip right>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                fab
                                outline
                                small
                                class="warning float-right"
                                v-if="isEventOrganizer"
                                v-on="on"
                            >
                                <v-icon color="warning">edit</v-icon>
                            </v-btn>
                        </template>
                        <span>Edit event</span>
                    </v-tooltip>
                </v-card-actions>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            color="primary"
                            dark
                            small
                            absolute
                            bottom
                            right
                            fab
                            to="/"
                            v-on="on"
                        >
                            <v-icon>home</v-icon>
                        </v-btn>
                    </template>
                    <span>Home</span>
                </v-tooltip>
            </v-card> 
        </v-flex>
    </v-layout>
</template>

<script>
import { mapActions, mapState } from 'vuex';
import store from '@/store/store' // Work around when $this is out of scope, import store to call fetchEvent located in event namespaced module
import { filter, isArray } from 'lodash';
import NProgress from "nprogress";


// beforeRouteEnter() is a In-Component Route Guard methods to incorporate a NProgress bar and can be used for may other functionality that is required either beforeRouteEnter/beforeRouteUpdate/beforeRouteLeave.

export default {
    props: {
        event: {
            type: Object,
            required: true
        }
    },
    computed: {
        category() {
            return this.event.category;
        },
        emailOrganizer() {
            return this.user.email ? 'mailto:' + this.user.email : 'mailto:richardlr23@gmail.com';
        },
        eventDate() {
            return this.event.event_date ? this.event.event_date : this.event.date;
        },
        eventDescription() {
            return this.event.description;
        },
        eventLocation() {
            return this.event.location; 
        },
        isEventOrganizer() {
            return this.event.user_id !== this.user.id ? false : true;
        },
        isUserRegistered() {
            return filter(this.event.attendees, {'user_id': this.user.id}).length;
        },
        time() {
            return this.event.event_time ? this.event.event_time : this.event.time;
        },
        title() {
            return this.event.event_name ? this.event.event_name : this.event.title;
        },
        ...mapState('user', ['user', 'token'])
    },
    created() {
        console.log(this.event.id); // R
    },
    methods: {
        eventSignUp() {
            const data = {
                'event_id': this.event.id,
                'user_id': this.user.id,
                'token': this.token
            };

            // We should be hiding the event sign up button if the signed in user is the event organizer or if the user is already signed up.  
            NProgress.start();
            this.signUp(data).then(response => {
                NProgress.done();
            }).then(error => {
                NProgress.done();
            });
        },
        ...mapActions('event', ['signUp']),
        ...mapActions('user', ['fetchUser'])
    }
}
</script>

<style scoped>
    .date-info {
        color: #00897B;
    }
    .event-header > .title {
        margin: 0;
    }
    .event-info {
        font-family: "Comic Sans MS", cursive, sans-serif;
        margin-bottom: 5px;
        padding: 10px;
    }
    .font-success {
        color: green;
    }
    .location {
        margin-bottom: 0;
    }
    .location > .icon {
        margin-left: 10px;
    }
    .list-group {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .list-group > .list-item {
        padding: 1em 0;
        border-bottom: solid 1px #e5e5e5;
    }
    .title {
        font-size: 20px!important;
    }
</style>