<template>
    <b-overlay
        id="overlay-computer-ubication"
        :show="getLoadingCensoStatus"
        rounded="sm"
        :opacity="0.75"
    >
        <NoConnectionAlert :status="onLine"></NoConnectionAlert>
        <b-container fluid>
            <b-row>
                <TowersVue
                    v-for="(tower, index) in getCensoInfo"
                    :key="index"
                    :towersstructure="tower"
                />
            </b-row>
        </b-container>
    </b-overlay>
</template>

<script>
import TowersVue from "./Towers.vue";
import NoConnectionAlert from "../alerts/NoConnectionAlert.vue";

export default {
    name: "UserOrientationComponent",
    components: {
        TowersVue,
        NoConnectionAlert
    },
    data() {
        return {
            structure: [],
            showOverlay: false,
            onLine: navigator.onLine,
            showBackOnline: false,
            json_meta: [
                [
                    {
                        key: "charset",
                        value: "utf-8"
                    }
                ]
            ]
        };
    },
    computed: {
        getCensoInfo() {
            return this.$store.state.uor.censoInfo;
        },
        getLoadingCensoStatus() {
            return this.$store.state.uor.isLoadingCenso;
        }
    },
    watch: {
        onLine(v) {
            if (v) {
                this.showBackOnline = true;
                setTimeout(() => {
                    this.showBackOnline = false;
                }, 1000);
            }
        }
    },
    mounted() {
        this.getInfraestructura();
    },
    methods: {
        getInfraestructura() {
            this.showOverlay = true;
            this.$store.dispatch("getCensoInfo");
        },
        updateOnlineStatus(e) {
            const { type } = e;
            this.onLine = type === "online";
        }
    },
    mounted() {
        window.addEventListener("online", this.updateOnlineStatus);
        window.addEventListener("offline", this.updateOnlineStatus);
    },
    created() {
        this.$store.dispatch("getCensoInfo");
    },
    beforeDestroy() {
        window.removeEventListener("online", this.updateOnlineStatus);
        window.removeEventListener("offline", this.updateOnlineStatus);
    }
};
</script>

<style></style>
