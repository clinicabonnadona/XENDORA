<template>
    <div>
        <b-modal
            v-model="showModalComponent"
            size="xl"
            no-close-on-backdrop
            no-close-on-esc
        >
            <template #modal-header-close>
                <b-button @click="closeModal()">X</b-button>
            </template>
            <b-row>
                <b-col>
                    <providerAgentsModalTemplate
                        v-if="modalTemplateName === 'provider-agents-template'"
                        :providerIdToShowModalComponent="
                            providerIdToShowModalComponent
                        "
                    ></providerAgentsModalTemplate>
                    <providerEvaluationModalTemplateVue
                        v-if="
                            modalTemplateName === 'provider-evaluation-template'
                        "
                        :providerIdToShowModalComponent="
                            providerIdToShowModalComponent
                        "
                    ></providerEvaluationModalTemplateVue>
                </b-col>
            </b-row>

            <template #modal-footer>
                <div>
                    <b-button
                        variant="primary"
                        size="sm"
                        class="float-right"
                        @click="closeModal"
                    >
                        Close
                    </b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import providerAgentsModalTemplate from "./providerEvaluation/providerAgentsModalTemplate.vue";
import providerEvaluationModalTemplateVue from "./providerEvaluation/providerEvaluationModalTemplate.vue";

export default {
    name: "modalTemplate",
    props: {
        showModalComponent: Boolean,
        modalTemplateName: String,
        providerIdToShowModalComponent: Number
    },
    components: {
        providerAgentsModalTemplate,
        providerEvaluationModalTemplateVue
    },
    data() {
        return {};
    },
    methods: {
        closeModal() {
            this.$emit("close");
        }
    }
};
</script>
