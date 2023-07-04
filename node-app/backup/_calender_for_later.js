import { google } from "googleapis"


const Auth2Client = new google.auth.OAuth2(
    process.env.CLIENT_ID,
    process.env.CLIENT_SECRET,
    process.env.REDIRECT_URL
)

app.get('/google', async (req, res) => {
    const scopes = ["profile", "email", "https://www.googleapis.com/auth/calendar"]
    const authUrl = Auth2Client.generateAuthUrl({
        access_type: 'offline',
        scope: scopes,
    });
    res.redirect(authUrl)
})

app.get('/google-redirect', async (req, res) => {
    const { code } = req.query
    try {
        const data = await Auth2Client.getToken(code)
        console.log(data)
        const { tokens } = data
        Auth2Client.setCredentials(tokens)
        res.redirect('/delete-event')
    } catch (error) {
        console.error('Error exchanging authorization code for tokens:', error)
        res.status(500).json({ error: 'Failed to exchange authorization code for tokens' })
    }
})

app.get('/add-event', async (req, res) => {
    const calendar = google.calendar({ version: 'v3', auth: Auth2Client })
    const event = {
        summary: 'PHP Interview Scheduled',
        start: {
            dateTime: '2023-06-12T10:00:00',
            timeZone: 'Asia/Kolkata',
        },
        end: {
            dateTime: '2023-06-16T20:00:00',
            timeZone: 'Asia/Kolkata',
        },
    }
    try {
        const response = await calendar.events.insert({
            calendarId: 'primary',
            resource: event,
        });
        console.log('Event added:', response.data.summary)
        res.send('Event added successfully')
    } catch (error) {
        console.error('Error adding event:', error)
        res.status(500).send('Error adding event')
    }
})

app.get('/list-event', async (req, res) => {
    const calendar = google.calendar({ version: 'v3', auth: Auth2Client });

    const params = {
        calendarId: 'primary', // Assuming you want to list events from the primary calendar
        timeMin: new Date().toISOString(), // Optional: Set a minimum time to filter events
        maxResults: 10, // Optional: Set the maximum number of events to retrieve
        singleEvents: true, // Optional: Expand recurring events into individual instances
        orderBy: 'startTime', // Optional: Order events by start time
    }

    calendar.events.list(params, (err, response) => {
        if (err) {
            console.error('Error retrieving events:', err)
            return;
        }
        const events = response.data.items;
        if (events.length) {
            events.forEach((event) => {
                console.log('Event ID:', event.id)
            });
        } else {
            console.log('No events found.')
        }
        res.json(events)
    })
})

app.get('/delete-event', (req, res) => {
    const calendar = google.calendar({ version: 'v3', auth: Auth2Client });

    calendar.events.delete({
        calendarId: 'primary',
        eventId: "dh07va81u8bt8sqfc40lqp888k",
    }, (err) => {
        if (err) {
            console.error('Error deleting event:', err)
            return
        }
        console.log('Event deleted successfully.')
    })
})