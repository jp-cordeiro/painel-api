<template>
    <v-container grid-list-sm class="senhas px-0 py-0">
        <v-layout row wrap>
            <v-flex sm12>
                <v-list class="lista-senhas py-1 px-0"
                        v-for="senha in senhasChamadas"
                        :key="senha.id">
                    <v-flex sm12>
                        <v-card class="card-senha text-uppercase py-0 px-0" :class="senha.tipo_senha.cor">
                            <v-card-title>
                                <v-flex sm12 class="titulo-card text-sm-center">
                                    {{ senha.senhaCompleta }}
                                </v-flex>
                                <hr class="separador"/>
                                <v-flex sm12 class="subtitulo-card text-sm-center">
                                    {{senha.guiche.descricao}}
                                </v-flex>
                            </v-card-title>
                        </v-card>
                    </v-flex>
                </v-list>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import { mapGetters,mapActions } from 'vuex'
    import fabricaSenhasPainel from './factories/senhaPainelFactory'

    export default {
        data(){
            return{
                alturaCard: 0,
                textoCard:{
                    fontSize: 2 + 'rem'
                }
            }
        },
        computed:{
            ...mapGetters({
                senhasChamadas: 'getSenhasChamadas',
                ultimaSenhaChamada: 'getUltimaSenhaChamada',
            })
        },
        methods:{
            ...mapActions({
                loadSenhasChamadas: 'loadSenhasChamadas',
                setSenhasChamadas: 'setSenhasChamadas',
                setChamadaAtual: 'setChamadaAtual',
                setUltimaSenhaChamada: 'setUltimaSenhaChamada'
            })
        },
        async created(){
            const senhasApi = await this.loadSenhasChamadas()

            const senhasFabricadas = fabricaSenhasPainel.fabricarSenhasPainel(senhasApi)

            this.setSenhasChamadas(senhasFabricadas)
        },
        mounted(){
            Echo.channel('senha-chamada')
                .listen('SenhaChamada', senha => {
                    console.log(senha)
                    senha.senhaCompleta = `${senha.tipo_senha.prefixo} - ${senha.numero}`

                    if(this.ultimaSenhaChamada && this.ultimaSenhaChamada.id != senha.id){
                        let senhaExistente = this.senhasChamadas.find( senha => {
                            if(senha.id == this.ultimaSenhaChamada.id){
                                return senha;
                            }
                        })

                        if(!senhaExistente){
                            /*
                            Caso já exista uma senha chama e o array tenha 4 elementos
                             */
                            if(this.ultimaSenhaChamada && this.senhasChamadas.length == 4){
                                this.senhasChamadas.splice(3,1)
                            }

                            this.senhasChamadas.unshift(this.ultimaSenhaChamada)
                        }

                        console.log(senhaExistente)
                        console.log(senha)

                    }

                    this.setUltimaSenhaChamada(senha)
                })
        }
    }
</script>

<style scoped>
    .senhas{
        position: absolute;
        top:0;
        left:70%;
        width: 30%;
        height: 100%;
    }

    .card-senha{
        border-radius: 25px;
        color: #fff;
    }

    .titulo-card{
        color: #fff;
        font-size: 3rem;
    }

    .subtitulo-card{
        color: #fff;
        font-size: 2rem;
    }

    .lista-senhas{
        overflow-y: auto;
    }

    .separador{
        width:100%
    }
</style>
