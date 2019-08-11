<template>
    <nav>
        <v-toolbar class="grey darken-3" dark app>
            <v-toolbar-side-icon @click="drawer = !drawer"></v-toolbar-side-icon>

            <v-toolbar-title class="headline text-uppercase" to="/">
                <v-icon class="pb-1">local_florist</v-icon>
                <span> Local</span>
                <span class="font-weight-light">Community Events</span>
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <div class="text-xs-right">
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn rounded color="warning" v-on="on" to="/">
                            <v-icon class="mr-1">event</v-icon>
                            <span>Events</span>
                        </v-btn>                
                    </template>
                    <span>View all community events</span>
                </v-tooltip>

            </div>

            <div class="text-xs-right pr-3">
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn fab small class="primary" v-on="on" to="/event/create">
                            <v-icon>add</v-icon>
                        </v-btn>
                    </template>
                    <span>Add an event</span>
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
    export default {
        data() {
            return {
                drawer: false
            }
        }
    }
</script>

<style scoped>

</style>