<template>
    <div>
        <b-card no-body
                header-bg-variant="dark"
        >
            <template #header>
                <h4 class="text-center font-weight-bolder text-white">VARIABLES DE ROTACIÓN</h4>
            </template>

            <b-list-group flush>
                <b-list-group-item>
                    <strong>SALDO: {{ saldo }}</strong>
                </b-list-group-item>
                <b-list-group-item>
                    <strong>PROMEDIO:</strong>
                </b-list-group-item>
                <b-list-group-item variant="danger">
                    <strong>MIN:</strong>
                </b-list-group-item>
                <b-list-group-item variant="success">
                    <strong>MAX:</strong>
                </b-list-group-item>
                <b-list-group-item>
                    <strong>$ ULTIMO MES:</strong>
                </b-list-group-item>
            </b-list-group>

        </b-card>
    </div>
</template>

<script>
export default {
    name: "RotacionVariables",
    data() {
        return {
            saldo: 0,
            min: 0,
            max: 0
        }
    },
    /*created() {
        this.saldo = this.$store.state.saldo
    }*/
}
</script>

<style scoped>

</style>
