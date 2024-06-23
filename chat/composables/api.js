import axios from "axios";

export function useApi() {
    const config = useRuntimeConfig()

    const axiosConfig = {
        baseURL: config.public.apiUrl,
        withCredentials: true
    }
    
    const http = axios.create(axiosConfig);

    http.interceptors.request.use((reqConfig) => {
        reqConfig.headers['Authorization'] = 'Bearer ' + config.public.token
        reqConfig.headers['X-Requested-With'] = 'XMLHttpRequest'
        return reqConfig
    }, (error) => {
        return error;
    })

    return http;
}