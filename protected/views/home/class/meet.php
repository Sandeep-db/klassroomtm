<style>
    .playing-video {
        height: 100%;
        transform: rotateY(180deg);
        -webkit-transform: rotateY(180deg);
        /* Safari and Chrome */
        -moz-transform: rotateY(180deg);
        /* Firefox */
    }

    .display-6 {
        font-size: 28px !important;
    }
</style>

<script defer src="https://unpkg.com/peerjs@1.4.7/dist/peerjs.min.js"></script>
<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>

<div class="p-4">
    <div class="d-flex justify-content-center">
        <div class="col-11 bg-light shadow-lg rounded d-flex flex-row-reverse justify-content-between" style="height: 700px" id="meet-video">
            <div class="people col-4 m-1">

            </div>
        </div>
    </div>
</div>

<script>
    function getUserDivHtml(userId, user_name) {
        let div = `
            <div class="col-12 bg-dark rounded d-flex my-1" id="${userId}">
                <div class="bg-light rounded-circle m-2" style="height: 50px; width: 50px">

                </div>
                <div class="d-flex flex-column rounded justify-content-center">
                    <p class="display-6 text-light">${user_name}</p>
                </div>
            </div>    
        `
        return div
    }

    function connectSocket() {
        let peers = {}
        let user_name = "<?= $_COOKIE['name'] ?>"
        let class_id = "<?= "c797a3e0-dd3a-456b-bc46-008bc3b44dd7" ?>"
        let user_email = "<?= $_COOKIE['email'] ?>"

        console.log("Connecting")
        let socket = io('http://172.16.112.227:4000')

        const myPeer = new Peer(undefined, {
            host: '/',
            port: 3001
        })

        const videoGrid = document.getElementById('meet-video')
        const myVideo = document.createElement('video')
        myVideo.setAttribute('class', 'playing-video rounded')
        myVideo.muted = true
        navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true,
        }).then(stream => {
            addVideoStream(myVideo, stream)
            myPeer.on('call', call => {
                call.answer(stream)
                const video = document.createElement('video')
                video.setAttribute('class', 'playing-video rounded')
                call.on('stream', newStream => {
                    addVideoStream(video, newStream)
                })
                call.on('close', () => {
                    video.remove()
                })
            })
            socket.on('meet-joined', data => {
                let div = getUserDivHtml(data.id, data.user_name)
                $('#' + data.id).remove()
                $('.people').append(div)
                setTimeout(() => {
                    $('#' + data.id).on('click', () => {
                        addNewUser(data.id, stream)
                    })
                    console.log(data.id)
                }, 1000)
            })
        })

        socket.on('user-disconnected', userId => {
            if (peers[userId]) peers[userId].close()
        })

        myPeer.on('open', id => {
            socket.emit('join-meet', class_id, user_name, user_email, id)
        })

        function addNewUser(user_id, stream) {
            $('.playing-video').remove()
            const call = myPeer.call(user_id, stream)
            const video = document.createElement('video')
            video.setAttribute('class', 'playing-video')
            call.on('stream', newStream => {
                addVideoStream(video, newStream)
            })
            call.on('close', () => {
                $('#' + user_id).remove()
                video.remove()
            })
            peers[user_id] = call
        }

        function addVideoStream(video, stream) {
            video.srcObject = stream
            video.addEventListener('loadedmetadata', () => {
                video.play()
            })
            videoGrid.append(video)
        }

    }

    $(document).ready(function() {
        $('#class-nav').show()
        $('#class-name').text('<?= $class_id ?>')
        $('#main-body-div').attr('style', 'padding: 0 !important; height: 775px; overflow-y: scroll;')
        connectSocket()
    })
</script>