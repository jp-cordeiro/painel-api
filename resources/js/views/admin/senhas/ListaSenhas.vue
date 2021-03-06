<template>
    <div>
        <v-container grid-list-sm class="px-0 py-0 mb-3">
            <v-layout wrap justify-end>
                <v-flex sm2>
                    <v-toolbar-title class="headline text-uppercase mr-4">
                        <span>Senhas </span>
                    </v-toolbar-title>
                </v-flex>
                <v-flex sm3>
                    <!--<InputSearch :param="busca"></InputSearch>-->
                    <v-text-field
                            class="mr-1"
                            v-model="busca"
                            append-icon="search"
                            label="Pesquisar"
                            single-line
                            hide-details
                    ></v-text-field>
                </v-flex>
                <v-flex sm2>
                    <v-btn @click.prevent="chamarSenha" color="primary darken-3">Chamar</v-btn>
                </v-flex>
                <v-flex sm3>
                    <v-btn @click.prevent="chamarNovamente" color="primary darken-3">Chamar Novamente</v-btn>
                </v-flex>
                <v-flex sm2>
                    <v-btn @click.prevent="atender" color="primary darken-3">Atender</v-btn>
                </v-flex>
            </v-layout>
        </v-container>
        <v-flex sm12>
            <v-data-table
                    :headers="cabecalho"
                    :items="senhas"
                    :load="load"
                    :search="busca"
                    :rows-per-page-items="[10,25,50]"
                    :disable-initial-sort="true"
                    class="elevation-1"
            >
                <template slot="items" slot-scope="props">
                    <tr @dblclick="selecionarLinha($event, props.item)" :class="{'primary lighten-3': senhaSelecionada.id == props.item.id}">
                        <td>{{ props.item.senhaCompleta }}</td>
                        <td>{{ props.item.descricao }}</td>
                        <td><div :class="props.item.cor" class="cor-tipo"></div></td>
                        <td v-show="props.item.selecionado">
                            <v-btn color="primary darken-3">Chamar senha {{ props.item.prefixo }} {{ props.item.numero }}</v-btn>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-flex>
        <Mensagens></Mensagens>
        <DialogPaciente></DialogPaciente>
    </div>
</template>

<script>
    import { mapGetters,mapActions } from 'vuex'
    import Mensagens from '../../../components/admin/views/Mensagens'
    import DialogPaciente from '../pacientes/DialogPaciente'

    export default {
        components:{
            Mensagens,
            DialogPaciente
        },
        data(){
            return{
                cabecalho:[
                    {
                        text: 'Senha',
                        align: 'left',
                        value: 'senhaCompleta',
                        sortable:false
                    },
                    {
                        text:'Descrição',
                        value:'descricao',
                        sortable:false
                    },
                    {
                        text:'Cor',
                        value:'cor',
                        sortable:false
                    }
                ],
                load: true,
                busca: '',
                senhaSelecionada:{
                    selecionado:false
                }
            }
        },
        computed:{
            ...mapGetters({
                senhas: 'getSenhas',
                guiche: 'getGuicheSelecionado'
            }),
            senha:{
                get(){
                    return this.$store.getters.getSenha
                },
                set(value){
                    this.$store.dispatch('setSenha',value)
                }
            }
        },
        methods:{
            ...mapActions({
                loadSenhas: 'loadSenhas',
                chamarProximo: 'chamarProximo',
                chamarSenhaNovamente: 'chamarSenhaNovamente',
                atenderSenha: 'atenderSenha',
                setMensagem: 'setMensagem'
            }),
            selecionarLinha(e, linhaSelecionada){
                if(linhaSelecionada.selecionado){
                    linhaSelecionada.selecionado = false
                    e.target.parentElement.classList.remove('primary','lighten-3')
                    this.senhaSelecionada = {}
                }
                else {
                    linhaSelecionada.selecionado = true
                    this.senhaSelecionada = linhaSelecionada
                }
            },
            async chamarSenha(){
                let chamadaObj = {
                    guiche_id: this.guiche.id
                }

                try{
                    const senha = await this.chamarProximo(chamadaObj)

                    this.verificarArraySenha(senha)

                    this.loadSenhas()
                }catch(e){
                    this.setMensagem({
                        texto: e.message,
                        tipo: 'error darken-2',
                        ativo: true
                    })
                }
            },
            async chamarNovamente(){
                let chamadaObj = {
                    guiche_id: this.guiche.id
                }

                try{
                    const senha = await this.chamarSenhaNovamente(chamadaObj)

                    this.verificarArraySenha(senha)

                    this.loadSenhas()
                }catch(e){
                    this.setMensagem({
                        texto: e.message,
                        tipo: 'error darken-2',
                        ativo: true
                    })
                }
            },
            async atender(){
                // let chamadaObj = {
                //     guiche_id: this.guiche.id
                // }
                //
                // try{
                //     const senha = await this.atenderSenha(chamadaObj)
                //
                //     console.log(senha)
                //
                //     this.loadSenhas()
                // }catch(e){
                //     this.setMensagem({
                //         texto: e.message,
                //         tipo: 'error darken-2',
                //         ativo: true
                //     })
                // }
                if(this.senhas.length > 0){
                    this.$store.dispatch('setDialogPaciente',true)
                }else{
                    this.setMensagem({
                        texto: "Não há senhas para serem atendidas.",
                        tipo: 'success darken-2',
                        ativo: true
                    })
                }
            },
            verificarArraySenha(senhas){
                console.log(senhas)
                if(Array.isArray(senhas)){
                    this.setMensagem({
                        texto: senhas,
                        tipo: 'success darken-2',
                        ativo: true
                    })
                }
            }
        },
        watch:{
            senhaSelecionada:{
                handler: function(novoValor,valorAntigo){
                    valorAntigo.selecionado = false
                    novoValor.selecionado = true
                },
                deep:true
            }
        },
        created(){
            this.loadSenhas()
        },
        mounted(){
            Echo.channel('senha-gerada')
                .listen('SenhaGerada', senha => {
                    senha.senhaCompleta = senha.prefixo + senha.numero.toString()
                    this.senhas.push(senha)
                })
        }
    }
</script>

<style scoped>
    .linha-selecionada{
        background-color: #616161;
        color: #fff;
    }
</style>
