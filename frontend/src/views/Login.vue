<template>
    <v-container fluid fill-height>
        <v-layout>
            <v-flex xs12>
                <v-card align-center class="mx-auto mt-5">
                    <v-card-title>
                        <v-toolbar color="primary" dark>
                            <v-toolbar-title class="login-styles title">
                                    <span class="ml-1">
                                        <v-icon class="mr-2" size="30">fingerprint</v-icon>
                                        <b>Login</b>
                                    </span>
                            </v-toolbar-title>
                        </v-toolbar>
                    </v-card-title>
                    <v-card-text>
                        <v-form
                            v-model="valid"
                            :lazy-validation="lazy"
                        >
                            <v-text-field
                                clearable
                                class="mb-3"
                                label="Email"
                                prepend-icon="account_circle"
                                :rules="[rules.email, rules.required]"
                                v-model="loginInformation.email"
                            />
                            <v-text-field
                                clearable 
                                label="Password"
                                prepend-icon="lock"
                                :append-icon="showPassword ? 'visibility' :  'visibility_off'"
                                :rules="[rules.min, rules.required]"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="loginInformation.password"
                                @click:append="showPassword = !showPassword"
                            />
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn
                            color="success"
                            to="/register"
                        >
                            <v-icon class="mr-1">how_to_reg</v-icon> Register now
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                            class="info"
                            :disabled="!valid"
                            :loading="isLogging"
                            @click="userLogin"
                        >
                            <v-spacer></v-spacer>
                            Login <v-icon size="20">arrow_forward</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import { mapActions } from 'vuex';
    import NProgress from "nprogress"; 
    
    export default {
        data() {
            return {
                email: '',
                isLogging: false,
                lazy: false,
                loginInformation: this.userLoginInstance(),
                password: '',
                rules: {
                    required: value => !!value || 'Required.',
                    min: value => {
                        if (value) {
                            if (value.length <= 8) {
                                return 'Minimum of 8 characters.';
                            } else {
                                return true;
                            }
                        } else {
                            return true;
                        }
                    },
                    email: value => {
                        if (value) {
                            if (value.length > 0) {
                                const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                                return pattern.test(value) || 'Invalid email.';
                            } 
                        } else {
                            return 'Invalid email.';
                        }
                    }
                },
                showPassword: false,
                valid: false
            }
        },
        methods: {
            userLogin() {
                this.isLogging = true;
                NProgress.start();
                this.login(this.loginInformation).then(() => {
                    this.$router.push({
                        name: 'event-list',
                        params: { page: 1 }
                    });
                }).catch(() => {
                    NProgress.done();
                });
            },
            userLoginInstance() {
                return {
                    email: this.email,
                    password: this.password
                }
            },
            ...mapActions('user', ['login'])
        }
    }
</script>

<style scoped>
    .login-styles {
        font-family: "Comic Sans MS", cursive, sans-serif;
    }
</style>