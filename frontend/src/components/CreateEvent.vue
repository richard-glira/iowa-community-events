<template>
    <v-container class="col-6" fluid fill-height>
        <v-layout align-center justify-center>
            <v-flex>
                <v-form 
                    v-model="valid"
                    :lazy-validation="lazy"
                    @submit.prevent="createEvent"
                >
                    <div class="align-center">
                        <h1 class="-fill-gradient text-xs-center"><v-icon class="mr-1 pb-1">playlist_add</v-icon> Create Community Event</h1>                        
                    </div>

                    <v-text-field
                        label="Event Name *"
                        prepend-icon="spa"
                        v-model="event.title"
                        :rules="[rules.required, rules.min]"
                        required 
                        clearable
                    ></v-text-field>

                    <v-text-field
                        label="Description *"
                        prepend-icon="description"
                        v-model="event.description"
                        :rules="[rules.required]"
                        required
                        clearable
                    ></v-text-field>

                    <v-select
                        :items="categories"
                        label="Select a category *"
                        prepend-icon="category"
                        v-model="event.category"
                        :rules="[rules.required]"
                        clearable
                    ></v-select>

                    <v-text-field
                        label="Location *"
                        prepend-icon="place"
                        v-model="event.location"
                        :rules="[rules.required]"
                        required
                        clearable
                    ></v-text-field>

                    <v-select 
                        :items="times"
                        label="Select a time *"    
                        prepend-icon="access_time"
                        v-model="event.time"
                        :rules="[rules.required]"
                        clearable
                    ></v-select>

                    <v-text-field
                        label="Contact E-mail *"
                        prepend-icon="email"
                        v-model="event.email"
                        :rules="[rules.required, rules.email]"
                        required
                        clearable
                    ></v-text-field>

                    <v-menu
                        ref="menu"
                        v-model="menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        lazy
                        transition="scale-transition"
                        offset-y
                        full-width
                        min-width="290px"
                    >
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="dateFormatted"
                                label="Select a date *"
                                prepend-icon="event"
                                :rules="[rules.required]"
                                @blur="date = parseDate(dateFormatted)"
                                v-on="on"
                                readonly 
                                persistent-hint
                            ></v-text-field>
                        </template>
                        <v-date-picker :min="currentDate" v-model="date" @input="menu = false"></v-date-picker>
                    </v-menu>
                </v-form>
                <v-btn 
                    class="button cancel-create-button success pl-2 ml-0 float-right"
                    :disabled="!valid"
                    @click="createLocalEvent" 
                >
                    <span>
                        <v-icon class="mr-1" left>add_circle_outline</v-icon>Create
                    </span>
                </v-btn>
                
                <v-btn 
                    class="error cancel-create-button pl-2 float-right"
                    to="/"
                >
                    <span>
                        <v-icon class="mr-1">remove_circle_outline</v-icon>Cancel
                    </span>
                </v-btn>
            </v-flex>
        </v-layout>
    </v-container>        
</template>

<script>
    import { mapActions, mapState } from 'vuex';
    import store from '@/store/store';
    import NProgress from "nprogress";
    import  Datepicker  from 'vuejs-datepicker';
    import { required } from 'vuelidate/lib/validators';

    export default {
        components: {
            Datepicker
        },
        computed: {
            currentDate() {
                var date = new Date();
                return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
            },
            ...mapState('user', ['token', 'user'])
        },
        data() {
            const date = '',
                  dateFormatted = '',
                  menu = false,
                  merridiem = 'am',
                  timeIndex = 0,
                  times = []

            for (let i = 1; i <= 24; i++) {
                times.push(i + ':00');
            }

            return {
                categories: this.$store.state.categories,
                date,
                dateFormatted,
                event: this.createEventInstance(),
                lazy: false,
                menu,
                times,
                rules: {
                    required: value => !!value || 'Required.',
                    min: v => v.length >= 8 || 'Minimum of 8 characters.',
                    email: value => {
                        if (value) {
                            if (value.length > 0) {
                                const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                                
                                return pattern.test(value) || 'Invalid e-mail.';
                            }
                        } else {
                            return 'Invalid e-mail.';
                        }
                    }
                },
                valid: false
            }
        },
        methods: {
            createLocalEvent() {
                NProgress.start();
                this.event.token = this.token;
                this.createEvent(this.event).then(() => {
                    this.$router.push({
                        name: 'event-show',
                        params: { id: this.event.id }
                    });
                }).catch(() => {
                    NProgress.done();
                });
            },
            createEventInstance() {
                const id = Math.floor(Math.random() * 10000000);
                const user = this.$store.state.user.user;

                return {
                    attendees: [],
                    category: '',
                    date: this.dateFormatted,
                    description: '',
                    email: '',
                    id: id,
                    location: '',
                    organizer: user,
                    time: '',
                    title: ''
                }
            },
            formatDate(date) {
                if (!date) return null

                this.event.date = date
                const [year, month, day] = date.split('-')
                return `${month}/${day}/${year}`
            },
            parseDate(date) {
                if (!date) return null

                this.event.date = date
                const [month, day, year] = date.split('/')
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
            },
            ...mapActions('event', ['createEvent'])
        },
        validations: {
            event: {
                category: { required },
                organizer: { required },
                title: { required },
                description: { required },
                location: { required },
                date: { required },
                time: { required }, 
                email: { required }
            }
        },
        watch: {
            date(val) {
                this.dateFormatted = this.formatDate(this.date)
            }
        },
    }
</script>

<style scoped>
    .button.-fill-gradient {
        background: linear-gradient(to right, #16c0b0, #84cf6a);
        color: #ffffff;
    }
    .cancel-create-button {
        border-radius: 10%;
    }
    .errorMessage {
        color: red;
    }
    .font-error {
        color: red;
    }
    .font-success {
        color: green;
    }
</style>