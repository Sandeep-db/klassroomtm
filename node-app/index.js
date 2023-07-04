import express from "express"
import { Server } from "socket.io"
import cors from "cors"
import http from "http"
import Bard, { askAI } from "bard-ai"
import dotenv from "dotenv"
import axios from "axios"
dotenv.config()

const app = express()

app.use(express.json())
app.use(cors())


app.get('/', (req, res) => {
    res.json({ 'message': 'Bard Running' })
})

app.post('/bard', async (req, res) => {
    let { query } = req.body
    console.log(query)
    let response = await askAI(query)
    console.log(response)
    res.json(response)
})

app.post('/evaluate', async (req, res) => {
    let { links } = req.body
    console.log(links)
    let request_data = await readFileFromS3(links)
    request_data += "\nevaluate the above code if any mistakes and summarize\n"
    console.log(request_data)
    let response = await askAI(request_data)
    res.json(response)
})

async function readFileFromS3(links) {
    let total_code = "\n"
    for (let obj of links) {
        try {
            const response = await axios.get(obj.imageURL)
            const fileContent = response.data
            total_code += fileContent + '\n\n\n'
        } catch (error) {
            console.log('Error:', error.message)
        }
    }
    return total_code
}

// --- socket ----

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

// --- socket end ---

Bard.init(process.env.GOOGLE_COOKIE).then(() => {
    app.listen(process.env.PORT, () => {
        console.log(`Server runnning on port ${process.env.PORT}`)
    })
    server.listen(process.env.SOCKET, () => {
        console.log(`Socket runnning on port ${process.env.SOCKET}`)
    })
})
