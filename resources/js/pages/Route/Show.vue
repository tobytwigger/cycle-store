<template>
    <c-app-wrapper :title="pageTitle" :action-sidebar="true">
        <v-tabs
            v-model="tab"
            centered
            grow
            icons-and-text
        >
            <v-tabs-slider></v-tabs-slider>

            <v-tab href="#tab-summary">
                Summary
                <v-icon>mdi-information</v-icon>
            </v-tab>

            <v-tab href="#tab-files">
                Files
                <v-icon>mdi-file-document-multiple</v-icon>
            </v-tab>

            <v-tab href="#tab-waypoints">
                Places
                <v-icon>mdi-map-marker</v-icon>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tab-summary">
                <v-row>
                    <v-col>
                        <v-row>
                            <v-col class="px-8 pt-8">
                                <div v-if="routeModel.description">
                                    {{ routeModel.description }}
                                </div>
                                <div v-else>
                                    No description
                                </div>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="px-8 pt-8">
                                <div v-if="routeModel.notes">
                                    {{ routeModel.notes }}
                                </div>
                                <div v-else>
                                    No notes
                                </div>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col class="px-8 pt-8">
<!--                                TODO-->
<!--                                <c-activity-location-summary v-if="hasStats" :started-at="humanStartedAt" :ended-at="humanEndedAt"></c-activity-location-summary>-->
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col>
                        <span v-if="routeModel.distance">Distance: {{routeModel.distance}}m.</span>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col class="pa-8">
                        <c-route-map :places="places.data" :geojson="routePath"></c-route-map>
                    </v-col>
                </v-row>
            </v-tab-item>
            <v-tab-item value="tab-files">
                <c-route-file-form-dialog :route-model="routeModel" title="Upload a file" text="Upload a new file">
                    <template v-slot:activator="{trigger,showing}">
                        <v-btn
                            color="secondary"
                            @click.stop="trigger"
                            :disabled="showing"
                        >
                            <v-icon>mdi-upload</v-icon>
                            Upload file
                        </v-btn>
                    </template>
                </c-route-file-form-dialog>
                <c-manage-route-media :route-model="routeModel"></c-manage-route-media>
            </v-tab-item>

            <v-tab-item value="tab-waypoints">

                <v-row
                    align="center"
                    justify="center">
                    <v-col>

                        <c-pagination-iterator :paginator="places" item-key="id">
                            <template v-slot:default="{item}">
                                <c-place-card :place="item">
                                    <template v-slot:icons>
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-btn
                                                    icon
                                                    @click="removeFromRoute(item)"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                >
                                                    <v-icon>mdi-minus</v-icon>
                                                </v-btn>
                                            </template>
                                            Remove from route
                                        </v-tooltip>
                                    </template>
                                </c-place-card>
                            </template>
                        </c-pagination-iterator>
                    </v-col>
                </v-row>


                <v-row
                    align="center"
                    justify="center">
                    <v-col>
                        <c-place-search ref="placeSearch" :route-id="routeModel.id" @addToRoute="addToRoute" title="Search for a place" button-text="Add to route">
                            <template v-slot:activator="{trigger,showing}">
                                <v-btn :disabled="showing" @click="trigger">
                                    Find Places
                                </v-btn>
                            </template>
                        </c-place-search>
                    </v-col>
                </v-row>
            </v-tab-item>

        </v-tabs-items>

        <template #sidebar>
            <v-list>
                <v-list-item>
                    <c-delete-route-button :route-model="routeModel"></c-delete-route-button>
                </v-list-item>
                <v-list-item v-if="!routeModel.file_id">
                    <c-upload-route-file-button :route-model="routeModel"></c-upload-route-file-button>
                </v-list-item>
                <v-list-item v-if="routeModel.file_id">
                    <v-btn link :href="route('file.download', routeModel.file_id)">
                        Download route file
                    </v-btn>
                </v-list-item>
                <v-list-item v-if="routeModel.file_id">
                    <v-btn link :href="route('route.download', routeModel.id)">
                        Download route
                    </v-btn>
                </v-list-item>
                <v-list-item>
                    <v-btn link :href="route('planner.edit', routeModel.id)">
                        Edit Route
                    </v-btn>
                </v-list-item>
                <v-list-item>
                    <c-route-form :old-route="routeModel" title="Edit route" button-text="Update">
                        <template v-slot:activator="{trigger,showing}">
                            <v-btn :disabled="showing" @click="trigger">
                                Edit Route Metadata
                            </v-btn>
                        </template>
                    </c-route-form>
                </v-list-item>
            </v-list>
        </template>
    </c-app-wrapper>
</template>

<script>
import moment from 'moment';
import CAppWrapper from 'ui/layouts/CAppWrapper';
import CRouteFileFormDialog from 'ui/components/Route/CRouteFileFormDialog';
import CManageRouteMedia from 'ui/components/Route/CManageRouteMedia';
import CRouteMap from 'ui/components/Route/CRouteMap';
import CRouteForm from 'ui/components/Route/CRouteForm';
import CDeleteRouteButton from 'ui/components/Route/CDeleteRouteButton';
import CUploadRouteFileButton from 'ui/components/Route/CUploadRouteFileButton';
import CActivityLocationSummary from '../../ui/components/CActivityLocationSummary';
import CPaginationIterator from '../../ui/components/CPaginationIterator';
import CPlaceCard from '../../ui/components/Place/CPlaceCard';
import CPlaceSearch from '../../ui/components/Place/CPlaceSearch';

export default {
    name: "Show",
    components: {
        CPlaceSearch,
        CPlaceCard,
        CPaginationIterator,
        CActivityLocationSummary,
        CUploadRouteFileButton,
        CDeleteRouteButton, CRouteForm, CRouteMap, CManageRouteMedia, CRouteFileFormDialog, CAppWrapper},
    props: {
        routeModel: {
            required: true,
            type: Object
        },
        places: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            tab: 'tab-summary'
        }
    },
    methods: {
        formatDateTime(dt) {
            return moment(dt).format('DD/MM/YYYY HH:mm:ss');
        },
        addToRoute(place) {
            console.log(place);
            this.$inertia.post(route('route.place.store', this.routeModel.id), {
                place_id: place.id
            }, {
                onSuccess: (page) => this.$refs.placeSearch.loadPlaces()
            });
        },
        removeFromRoute(place) {
            this.$inertia.delete(route('route.place.destroy', [this.routeModel.id, place.id]), {
                onSuccess: (page) => this.$refs.placeSearch.loadPlaces()
            });
        }
    },
    computed: {
        pageTitle() {
            return this.routeModel.name ?? 'New Route';
        },
        routePath() {
            return {
                type: 'LineString',
                coordinates: this.routeModel.path.linestring.map(c => [c.coordinates[0], c.coordinates[1]])
            }
        }
    }
}
</script>

<style scoped>

</style>
