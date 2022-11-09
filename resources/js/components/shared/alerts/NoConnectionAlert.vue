<template>
    <div>
        <b-alert
            :show="!status"
            dismissible
            variant="info"
            @dismissed="dismissCountDown=0"
            @dismiss-count-down="countDownChanged"
            id="noconnectionalert"
            class="d-flex justify-content-center"
        >
            POR FAVOR VERIFIQUE SU CONEXIÓN A LA RED {{ dismissCountDown }} seconds...
        </b-alert>
    </div>
</template>

<script>
export default {
    name: "NoConnectionAlert",
    props: {
        status: Boolean
    },
    data() {
        return {
            dismissSecs: 8,
            dismissCountDown: 10
        }
    },
    methods: {
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        showAlert() {
            this.dismissCountDown = this.dismissSecs
        }
    }
}
</script>

<style scoped>

</style>
