<template>
    <v-card align-center class="mx-auto mt-5">
        <v-container grid-list-md>
            <v-form 
                v-model="valid"
                :lazy-validation="lazy"
                @submit.prevent="register"
            >
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-card-title class="px-0">
                            <v-toolbar color="primary" dark>
                                <v-toolbar-title>
                                    <span>
                                        <span class="ml-1">
                                            <v-icon class="mr-2">how_to_reg</v-icon>
                                            <b>Register and get involved!</b>
                                        </span>
                                    </span>                               
                                </v-toolbar-title>
                            </v-toolbar>
                        </v-card-title>
                    </v-flex>
                </v-layout>
                <v-layout row wrap class="px-1">
                    <v-flex xs6>
                        <v-text-field
                            clearable
                            label="Full Name"
                            prepend-icon="emoji_people"
                            :rules="[rules.required]"
                            v-model="registration.name"
                        />
                    </v-flex>
                    <v-flex xs6>
                        <v-text-field
                            clearable 
                            label="Email"
                            prepend-icon="email"
                            :rules="[rules.email, rules.required]"
                            v-model="registration.email"
                        />
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field
                            clearable 
                            label="Password"
                            prepend-icon="lock"
                            :append-icon="showPassword ? 'visibility' :  'visibility_off'"
                            :rules="[rules.min, rules.required]"
                            :type="showPassword ? 'text' : 'password'"
                            v-model="registration.password"
                            @click:append="showPassword = !showPassword"
                        />
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field
                            clearable 
                            label="Password Confirmation"
                            prepend-icon="lock"
                            :append-icon="showPassword ? 'visibility' :  'visibility_off'"
                            :rules="[rules.min, rules.required, passwordConfirmationRule]"
                            :type="showPassword ? 'text' : 'password'"
                            v-model="registration.password_confirmation"
                            @click:append="showPassword = !showPassword"
                        />
                    </v-flex> 
                </v-layout>
            </v-form>
        </v-container>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
                color="success"
                :disabled="!valid"
                @click="register"
            >
                <v-icon class="mr-1">person_add</v-icon> Register
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
    import { mapActions } from 'vuex';
    import NProgress from "nprogress"; 

    export default {
        computed: {
            passwordConfirmationRule() {
                return () => (this.registration.password === this.registration.password_confirmation) || 'Password must match.';
            }
        },
        data() {
            return {
                fullName: '',
                email: '',
                lazy: false,
                password: '',
                passwordConfirmation: '',
                registration: this.userRegistrationInstance(),
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
            register() {
                NProgress.start();
                this.userRegistration(this.registration).then(() => {
                    this.$router.push({
                        name: 'login'
                    });
                }).catch(() => {
                    NProgress.done();
                });
            },
            userRegistrationInstance() {
                return {
                    name: this.fullName,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.passwordConfirmation
                }
            },
            ...mapActions('user', ['userRegistration'])
        }
    }
</script>

<style lang="scss" scoped>

</style>