import express from "express"
import { Server } from "socket.io"
import cors from "cors"
import http from "http"
import Bard, { askAI } from "bard-ai"
import dotenv from "dotenv"
import { v4 as uuid } from 'uuid'
dotenv.config()

const app = express()

app.use(express.json())
app.use(cors())

app.get('/', (req, res) => {
    res.json({ 'message': 'Bard Running' })
})

app.post('/bard', async (req, res) => {
    let { query } = req.body
    let response = await askAI(query)
    res.json(response)
})

let cache = new Map()
const server = http.createServer(app)
const io = new Server(server, {
    cors: {
        origin: "*",
    }
})

io.on('connection', (socket) => {
    console.log(socket.id)
    socket.on('join-meet', (meet_id, user_name, user_email, id) => {
        socket.join(meet_id)
        socket.to(meet_id).emit('meet-joined', { id, user_name })
        socket.on('disconnect', () => {
            socket.to(meet_id).emit('user-disconnected', id)
        })
        cache.set(user_email, id)
    })
})


Bard.init("WwhRVoNtI5jXp9QIBFLjnDDu8OQ7sEatl9xs8Ks_p6SGukLir46cv2Cska47xvgx1oKgug.").then(() => {
    app.listen(process.env.PORT, () => {
        console.log(`Server runnning on port ${process.env.PORT}`)
    })
    server.listen(process.env.SOCKET_PORT, () => {
        console.log(`Socket runnning on port ${process.env.SOCKET_PORT}`)
    })
})
