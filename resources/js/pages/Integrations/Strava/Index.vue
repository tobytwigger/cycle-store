<template>
    <c-app-wrapper title="Strava" :action-sidebar="true">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#tab-connection">Connection Health<v-icon>mdi-heart-pulse</v-icon></v-tab>
            <v-tab href="#tab-import">Imports<v-icon>mdi-import</v-icon></v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-connection">
                <v-alert
                    outlined
                    type="warning"
                    prominent
                    border="left"
                >
                    The dashboard is still in development and will be available soon.
                </v-alert>
            </v-tab-item>

            <v-tab-item value="tab-import">
                <v-alert
                    outlined
                    type="warning"
                    prominent
                    border="left"
                >
                    The dashboard is still in development and will be available soon.
                </v-alert>
            </v-tab-item>
        </v-tabs-items>
        <template #sidebar>
            <v-list>
                <v-list-item v-if="$page.props.permissions.indexOf('manage-strava-clients') > -1">
                    <v-btn @click="$inertia.get(route('strava.client.index'))">
                        Manage Clients
                    </v-btn>
                </v-list-item>
            </v-list>
        </template>
    </c-app-wrapper>

<!--    <table class="w-full">-->
<!--        <thead>-->
<!--        <tr class="text-md font-semibold tracking-wide text-left text-gray-900 bg-gray-100 uppercase border-b border-gray-600">-->
<!--            <th class="px-4 py-3">ID</th>-->
<!--            <th class="px-4 py-3">Limits Used</th>-->
<!--            <th class="px-4 py-3">Invitations</th>-->
<!--            <th class="px-4 py-3">Details</th>-->
<!--            <th class="px-4 py-3"></th>-->
<!--        </tr>-->
<!--        </thead>-->
<!--        <tbody class="bg-white">-->
<!--        <tr class="text-gray-700" v-for="client in clients">-->
<!--            <td class="px-4 py-3 border">-->
<!--                <div>-->
<!--                    {{ client.id }}-->
<!--                    <span v-if="client.enabled === false">Disabled</span>-->
<!--                </div>-->
<!--            </td>-->
<!--            <td class="px-4 py-3 border">-->
<!--                <div class="flex items-center text-sm">-->
<!--                    <div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </td>-->
<!--            <td class="px-4 py-3 border">-->
<!--                    <span v-if="client.invitation_link_uuid">-->
<!--                        {{ client.invitation_link }}.-->
<!--                        <span v-if="client.invitation_link_expired">-->
<!--                            Link Expired-->
<!--                        </span>-->
<!--                        <span v-else>-->
<!--                        Valid until {{ toDateTime(client.invitation_link_expires_at) }}-->
<!--                        </span>-->
<!--                    </span>-->
<!--                <span v-else>-->
<!--                    N/A-->
<!--                    </span>-->
<!--                <Link :href="route('strava.client.invite', client.id)" method="post" as="button" type="button">-->
<!--                    <div class="flex items-center text-sm font-semibold text-red-700">-->
<!--                        <div>Refresh</div>-->

<!--                        <div class="ml-1 text-indigo-500">-->
<!--                            <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </Link>-->
<!--            </td>-->





<!--            <td class="px-4 py-3 text-sm border">-->
<!--                <Link :href="route('strava.client.destroy', client.id)" method="delete" as="button" type="button">-->
<!--                    <div class="flex items-center text-sm font-semibold text-red-700">-->
<!--                        <div>Delete</div>-->

<!--                        <div class="ml-1 text-indigo-500">-->
<!--                            <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </Link>-->
<!--                <span v-if="client.is_connected === false">-->
<!--                        <a :href="route('strava.login', client.id)">Click to login</a>-->
<!--                    </span>-->
<!--                <span v-else>-->
<!--                        <Link :href="route('integration.destroy', 'strava')" method="delete" as="button" type="button">-->
<!--                            <div class="flex items-center text-sm font-semibold text-red-700">-->
<!--                                <div>Click to log out</div>-->
<!--                            </div>-->
<!--                        </Link>-->
<!--                    </span>-->

<!--                <Link v-if="client.enabled" :href="route('strava.client.disable', client.id)" method="post" as="button" type="button">-->
<!--                    <div class="flex items-center text-sm font-semibold text-red-700">-->
<!--                        <div>Disable</div>-->
<!--                    </div>-->
<!--                </Link>-->
<!--                <Link v-else :href="route('strava.client.enable', client.id)" method="post" as="button" type="button">-->
<!--                    <div class="flex items-center text-sm font-semibold text-red-700">-->
<!--                        <div>Enable</div>-->
<!--                    </div>-->
<!--                </Link>-->

<!--                <Link v-if="client.public" :href="route('strava.client.private', client.id)" method="post" as="button" type="button">-->
<!--                    <div class="flex items-center text-sm font-semibold text-red-700">-->
<!--                        <div>Make Private</div>-->
<!--                    </div>-->
<!--                </Link>-->
<!--                <Link v-else :href="route('strava.client.public', client.id)" method="post" as="button" type="button">-->
<!--                    <div class="flex items-center text-sm font-semibold text-red-700">-->
<!--                        <div>Make Public</div>-->
<!--                    </div>-->
<!--                </Link>-->
<!--            </td>-->
<!--        </tr>-->
<!--        </tbody>-->
<!--    </table>-->


<!--    toDateTime(value) {-->
<!--    if (value === null) {-->
<!--    return 'No Date';-->
<!--    }-->
<!--    return moment(value).format('DD/MM/YYYY HH:mm:ss');-->
<!--    }-->


<!--    addClient() {-->
<!--    this.newClientForm.post(route('strava.client.store'), {-->
<!--    onSuccess: () => {-->
<!--    this.newClientForm.reset();-->
<!--    this.isAddingClient = false;-->
<!--    }-->
<!--    });-->
<!--    }-->



<!--    <div>-->
<!--        <modal :closeable="true" :show="viewStravaSyncStatus" @close="viewStravaSyncStatus = false">-->

<!--            <div class="px-6 py-4">-->
<!--                <div class="text-lg">-->
<!--                    Strava Sync Status-->
<!--                </div>-->

<!--                <div class="mt-4">-->
<!--                    <p>-->
<!--                        We are currently refreshing the following data in the background.-->

<!--                        You can see if an activity is being loaded by viewing it.-->
<!--                    </p>-->
<!--                    <ul class="list-disc">-->
<!--                        <li>Kudos: {{activitiesLoadingKudos}}</li>-->
<!--                        <li>Comments: {{activitiesLoadingComments}}</li>-->
<!--                        <li>Stats: {{activitiesLoadingStats}}</li>-->
<!--                        <li>Photos: {{activitiesLoadingPhotos}}</li>-->
<!--                        <li>Basic Data: {{activitiesLoadingBasicData}}</li>-->
<!--                    </ul>-->

<!--                    <p>Photos and the raw activity files must be imported manually with the full export.</p>-->

<!--                    <p>There are {{activitiesWithoutFiles}} activities that are linked to Strava but are missing the raw recording</p>-->

<!--                    <p>There are {{activitiesWithoutPhotos}} activities that should have photos attached</p>-->

<!--                    <p v-if="timeUntilReady === null">-->
<!--                        Strava is ready-->
<!--                    </p>-->
<!--                    <p v-else>-->
<!--                        Strava will be available in {{formattedTimeUntilReady}}-->
<!--                    </p>-->
<!--                </div>-->
<!--            </div>-->

<!--            <div class="px-12 py-4 bg-gray-100 text-right">-->
<!--                <div>-->
<!--                    <button class="ml-4" type="button" @click="viewStravaSyncStatus = false">-->
<!--                        OK-->
<!--                    </button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </modal>-->

<!--        <a href="#" @click.prevent="viewStravaSyncStatus = true" class="text-sm text-gray-400">View sync status</a>-->
<!--    </div>-->


<!--    name: "StravaIntegrationAddon",-->
<!--    props: {-->
<!--    activitiesLoadingKudos: Number,-->
<!--    activitiesLoadingComments: Number,-->
<!--    activitiesLoadingStats: Number,-->
<!--    activitiesLoadingPhotos: Number,-->
<!--    activitiesLoadingBasicData: Number,-->
<!--    activitiesWithoutFiles: Number,-->
<!--    activitiesWithoutPhotos: Number,-->
<!--    timeUntilReady: {-->
<!--    required: false,-->
<!--    type: Number,-->
<!--    default: null-->
<!--    }-->
<!--    },-->
<!--    data() {-->
<!--    return {-->
<!--    viewStravaSyncStatus: false-->
<!--    }-->
<!--    },-->
<!--    computed: {-->
<!--    formattedTimeUntilReady() {-->
<!--    if(this.timeUntilReady === null) {-->
<!--    return null;-->
<!--    }-->
<!--    return moment.duration(moment().add(this.timeUntilReady, 'seconds').diff(moment())).humanize();-->
<!--    }-->
<!--    }-->

</template>

<script>
import CAppWrapper from 'ui/layouts/CAppWrapper';
export default {
    name: "Index",
    components: {CAppWrapper},
    props: {

    },
    data() {
        return {
            tab: null
        }
    }
}
</script>

<style scoped>

</style>
