<template>
    <nav>
        <v-toolbar class="grey darken-3" dark app>
            <!-- <v-toolbar-side-icon @click="drawer = !drawer"></v-toolbar-side-icon> -->
            <v-toolbar-title class="headline text-uppercase" to="/">
                <v-icon class="pb-1">local_florist</v-icon>
                <span> Local</span>
                <span class="font-weight-light">Community Events</span>
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <div class="text-xs-right">
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn 
                            fab
                            outline 
                            small
                            color="warning" 
                            v-if="isLoggedIn"
                            v-on="on" 
                            to="/"
                        >
                            <v-icon>event</v-icon>
                        </v-btn>                
                    </template>
                    <span>View all community events</span>
                </v-tooltip>
            </div>

            <div class="text-xs-right pr-3">
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn 
                            outline
                            class="button-round info"
                            color="info"
                            v-on="on"
                            v-if="isLoggedIn" 
                            to="/event/create"
                         >
                            <v-icon>add</v-icon> Create
                        </v-btn>
                    </template>
                    <span>Add an event</span>
                </v-tooltip>
            </div>

            <div class="text-xs-right pr-3">
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn 
                            outline
                            class="button-round success"
                            color="success"
                            v-if="!isLoginScreen && !isLoggedIn"
                            v-on="on" 
                            to="/login"
                         >
                            <v-icon>lock</v-icon> Login
                        </v-btn>
                    </template>
                    <span>Login</span>
                </v-tooltip>

                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn 
                            small
                            fab
                            flat
                            outline
                            class="button-round error"
                            color="error"
                            v-if="!isLoginScreen && isLoggedIn"
                            v-on="on" 
                            @click="userLogout"
                         >
                            <v-icon>exit_to_app</v-icon>
                        </v-btn>
                    </template>
                    <span>Logout</span>
                </v-tooltip>
            </div>
        </v-toolbar>

        <v-navigation-drawer 
            app 
            clipped
            disable-resize-watcher 
            hide-overlay 
            v-model="drawer"
        >
            <p>Testing</p>
        </v-navigation-drawer>
    </nav>
</template>

<script>
    import { mapActions, mapState } from 'vuex';
    import store from "@/store/store.js";
    import NProgress from 'nprogress';

    export default {
        computed: {
            isLoginScreen() {
                return this.$route.path === '/login';
            },
            ...mapState('user', ['isLoggedIn', 'token', 'user'])
        },
        data() {
            return {
                drawer: false
            }
        },
        methods: {
            userLogout() {
                const user = {
                    user_id: this.user.id,
                    token: this.token
                };

                console.log(user);

                NProgress.start();
                this.logout(user).then(() => {
                    this.$router.push({
                        name: 'login'
                    });
                }).catch(() => {
                    NProgress.done()
                });
            },
            ...mapActions('user', ['logout'])
        }
    }
</script>

<style scoped>
.button-round {
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
}
</style>