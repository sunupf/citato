// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: [
    '@nuxt/ui',
    'nuxt-svgo'
  ],
  runtimeConfig: {
    public: {
      token: "1|Y7Nw9KRhyRYhVqvqEWpSJgr3V92JaHTKoNFLR1Sv3d341811",
      apiUrl: "http://api.cac.test",
    }
  },
  devtools: { enabled: true },
  devServer: {
    port: 80,
    host: '0.0.0.0',
  },
})