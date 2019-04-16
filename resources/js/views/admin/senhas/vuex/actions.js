import axios from 'axios'

export default {
    setSenha({commit},senha){
        commit('setSenha',senha)
    },
    setSenhaChamada({commit},senha){
        commit('setSenhaChamada', senha)
    },
    async loadSenhas({commit}){
        try{
            const request = await axios.get('senhas')

            const senhasPainelApi = request.data.map( senha => {
                senha.senhaCompleta = senha.prefixo + senha.numero
                return senha
            })

            commit('setSenhas', senhasPainelApi)
        }catch (e){
            throw new Error(e.response.data.error)
        }

    },
    async loadSenha({commit},idSenha){
        try{
            const request = await axios.get(`senhas/${idSenha}`)

            commit('setSenha',request.data)
        }catch (e){
            throw new Error(e.response.data.error)
        }
    },
    async chamarProximo({commit}, dadosGuiche){
      try{
          const request = await axios.put(`senhas/chamarProximo`, dadosGuiche)

          commit('setSenhaChamada',request.data)

          return request.data
      }catch (e){
          throw new Error(e.response.data.error)
      }
    }
}