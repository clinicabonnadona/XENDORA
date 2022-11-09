<template>
    <div>
        <b-container fluid>
            <b-row>
                <b-col>
                    <div class="error-template">
                        <h1>
                            :) Oops!</h1>
                        <h2>
                            Este módulo se encuentra en mantenimiento</h2>
                        <h1>
                            Volveremos pronto!</h1>
                        <div>
                            — The Team</p>
                        </div>
                    </div>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
export default {
    name: "Maintenance",
    data() {
        return {}
    }
}
</script>

<style>
.error-template {
    padding: 40px 15px;
    text-align: center;
}
</style>
