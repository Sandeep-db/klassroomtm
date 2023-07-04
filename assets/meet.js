flag = 0
var conn = new WebSocket('ws://localhost:8888');
conn.onopen = async function (e) {
    await makemap();
    console.log("connection established");
    flag = 1;
};

conn.onerror = function (error) {
    console.log('WebSocket Error: ' + error);
};
var peer = new RTCPeerConnection({
    iceServers: [{
        urls: [
            "stun:stun.l.google.com:19302",
            "stun:global.stun.twilio.com:3478"
        ]
    }]
})
conn.onmessage = async function (e) {
    msg = JSON.parse(e.data);
    if (msg.event == "message") {
        var opid = document.getElementById("opid").value;
        if (msg.senderid == opid) {
            document.getElementById("messages").innerHTML += `
            <li class="clearfix">
                <div class="message-data">
                    <span class="message-data-time">`+ new Date().toString().substr(0, 24) + `</span>
                </div>
                <div class="message my-message">`+ msg.msg + `</div>
            </li>`;
        }
    }
    else if (msg.event == "videocall") {

        $("#chatarea").fadeOut();
        $("#videoarea").fadeIn();
        await peer.setRemoteDescription(msg.offer);
        $.ajax({
            url: "http://localhost/index.php/site/getuser/?myid=" + msg.myid,
            type: "GET",
            success: async function (data) {
                data = JSON.parse(data);
                document.getElementById("opid").value = msg.myid;
                document.getElementById("opponame").innerHTML = data.name;
                $("#username").html(`incoming call ffrom  ` + data.name + `...`)
            }
        })
    }
    else if (msg.event == "anserd") {
        $("#username").html(`call connected with ` + document.getElementById("opponame").innerHTML + `...`)
        peer.setRemoteDescription(msg.anser);
    }
    else if (msg.event == "negvideocall") {
        peer.setRemoteDescription(msg.offer)
        var anser = await peer.createAnswer(msg.offer);
        peer.setLocalDescription(anser);
        data = JSON.stringify({
            event: "negvideocallanser",
            anser: anser,
            opid: document.getElementById("opid").value
        })
        conn.send(data)
    }
    else if (msg.event == "negvideocallanser") {
        peer.setRemoteDescription(msg.anser)
    }
};
peer.ontrack = (e) => {
    document.getElementById("videostream2").srcObject = e.streams[0];
    return false;
};
async function anser() {
    $("#anser").fadeOut();
    $("#videoon").fadeIn("fast")
    $("#username").html(`call connected with  ` + document.getElementById("opponame").innerHTML + `...`)
    var anser = await peer.createAnswer(peer.remoteDescription);
    peer.setLocalDescription(anser);
    data = JSON.stringify({
        event: "anserd",
        anser: anser,
        opid: document.getElementById("opid").value
    });
    conn.send(data);
}
peer.addEventListener('negotiationneeded', async () => {
    var offer = await peer.createOffer()
    peer.setLocalDescription(offer)
    data = JSON.stringify({
        event: "negvideocall",
        offer: offer,
        opid: document.getElementById("opid").value
    });
    conn.send(data);
})
async function onvideo() {
    var stream = await navigator.mediaDevices.getUserMedia({
        video: true,
        audio: true
    })
    if (document.getElementById("videoon").name === "0") {
        document.getElementById("videoon").name = "1"
        document.getElementById("videoon").innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="50" height="30" fill="currentColor" class="bi bi-camera-video-off-fill" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M10.961 12.365a1.99 1.99 0 0 0 .522-1.103l3.11 1.382A1 1 0 0 0 16 11.731V4.269a1 1 0 0 0-1.406-.913l-3.111 1.382A2 2 0 0 0 9.5 3H4.272l6.69 9.365zm-10.114-9A2.001 2.001 0 0 0 0 5v6a2 2 0 0 0 2 2h5.728L.847 3.366zm9.746 11.925-10-14 .814-.58 10 14-.814.58z"/>
  </svg>`
        document.getElementById("videostream").srcObject = stream
        for (const track of stream.getTracks()) {
            peer.addTrack(track, stream);
        }

    }
    else {
        document.getElementById("videoon").name = "0"
        document.getElementById("videoon").innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="50" height="30" fill="currentColor" class="bi bi-camera-video-fill" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M0 5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 4.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 13H2a2 2 0 0 1-2-2V5z"/>
      </svg>`
        temp = document.getElementById("videostream")
        temp.srcObject.getVideoTracks().forEach(track => {
            track.enabled = false
        });
    }
}
async function onaudio() {
    var stream = await navigator.mediaDevices.getUserMedia({
        audio: true
    })
    if (document.getElementById("videoon").name === "0") {
        document.getElementById("videoon").name = "1"
        document.getElementById("videoon").innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="50" height="30" fill="currentColor" class="bi bi-mic-mute-fill" viewBox="0 0 16 16">
        <path d="M13 8c0 .564-.094 1.107-.266 1.613l-.814-.814A4.02 4.02 0 0 0 12 8V7a.5.5 0 0 1 1 0v1zm-5 4c.818 0 1.578-.245 2.212-.667l.718.719a4.973 4.973 0 0 1-2.43.923V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 1 0v1a4 4 0 0 0 4 4zm3-9v4.879L5.158 2.037A3.001 3.001 0 0 1 11 3z"/>
        <path d="M9.486 10.607 5 6.12V8a3 3 0 0 0 4.486 2.607zm-7.84-9.253 12 12 .708-.708-12-12-.708.708z"/>
      </svg>`
        document.getElementById("videostream").srcObject = stream
        for (const track of stream.getTracks()) {
            peer.addTrack(track, stream);
        }
    }
    else {
        document.getElementById("videoon").name = "0"
        document.getElementById("videoon").innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="50" height="30" fill="currentColor" class="bi bi-mic-fill" viewBox="0 0 16 16">
        <path d="M5 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0V3z"/>
        <path d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5z"/>
      </svg>`
        temp = document.getElementById("videostream")
        temp.srcObject.getVideoTracks().forEach(track => {
            track.enabled = false
        });
    }
}
function ajax(copy) {
    oppoid = copy.split(":")[0]
    name = copy.split(":")[1];
    document.getElementById("opponame").innerHTML = name;
    document.getElementById("opid").value = oppoid

    $.ajax({
        url: "http://localhost/index.php/site/messages/?opid=" + oppoid,
        contenttype: "application/json",
        type: "GET",
        success: async function (data) {
            data = JSON.parse(data);
            document.getElementById("chat").style.opacity = "1";
            str = ""
            var myid = document.getElementById("myid").value;
            for (message of data.messagesarray) {
                console.log(message);
                if (message.senderid.$oid === myid) {
                    str += `<li class="clearfix">
                        <div class="message-data text-right">
                            <span class="message-data-time">`+ message.time + `</span>
                            <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="avatar">
                        </div>
                        <div class="message other-message float-right">`+ message.message + `</div>
                    </li>`;
                }
                else {
                    str += ` <li class="clearfix">
                                <div class="message-data">      
                                    <span class="message-data-time">`+ message.time + `</span>
                                </div>
                                <div class="message my-message">`+ message.message + `</div>
                            </li>`;
                }
            }
            document.getElementById("messages").innerHTML = str;
        }
    })
}
function clicked() {
    var opid = document.getElementById("opid").value;
    if (opid != "chatgpt") {
        var senderid = document.getElementById("myid").value;
        var msg = document.getElementById("textmessage").value;
        data = { "event": "message", "opid": opid, "senderid": senderid, "msg": msg }
        conn.send(JSON.stringify(data));
    }
    else {
        var senderid = document.getElementById("myid").value;
        var msg = document.getElementById("textmessage").value;
        data = { "event": "messageforgpt", "opid": "chatgpt", "senderid": senderid, "msg": msg }
        conn.send(JSON.stringify(data));
    }
}
function makemap() {
    if (window.location.href.indexOf("chat") >= 0) {
        var myid = document.getElementById("myid").value;
    }
    var event = 'makemap';
    data = { "event": event, "myid": myid }
    conn.send(JSON.stringify(data));
}
async function videocall() {
    sessionStorage.setItem("videocallstart", 1);
    $("#chatarea").fadeOut();
    $("#videoarea").fadeIn();


    var offer = await peer.createOffer();
    await peer.setLocalDescription(offer);


    var event = "videocall";
    var callerid = document.getElementById("myid").value;
    var opid = document.getElementById("opid").value;
    callername =
        data = { "event": event, "offer": offer, "opid": opid, "myid": callerid }

    $("#username").html(`calling  ` + document.getElementById("opponame").innerHTML + `...`)
    $("#anser").hide()
    $("#videoon").fadeIn("fast")


    conn.send(JSON.stringify(data));

}
async function audiocall() {
    sessionStorage.setItem("videocallstart", 1);
    $("#chatarea").fadeOut();
    $("#audioarea").fadeIn();


    var offer = await peer.createOffer();
    await peer.setLocalDescription(offer);


    var event = "videocall";
    var callerid = document.getElementById("myid").value;
    var opid = document.getElementById("opid").value;
    callername =
        data = { "event": event, "offer": offer, "opid": opid, "myid": callerid }

    $("#username").html(`calling  ` + document.getElementById("opponame").innerHTML + `...`)
    $("#anser").hide()
    $("#videoon").fadeIn("fast")
    conn.send(JSON.stringify(data));
}
function chatgpt() {
    document.getElementById("opid").value = "chatgpt";
    document.getElementById("opponame").innerHTML = "chatgpt";
    $.ajax({
        url: "http://localhost/index.php/site/chatgpt",
        type: "GET",
        success: function (data) {
            data = JSON.parse(data);
            document.getElementById("chat").style.opacity = "1";
            str = ""
            if (data.messagesarray == null) data.messagesarray = [];
            var myid = document.getElementById("myid").value;
            for (message of data.messagesarray) {
                if (message.senderid.$oid === myid) {
                    str += `<li class="clearfix">
                        <div class="message-data text-right">
                            <span class="message-data-time">`+ message.time + `</span>
                            <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="avatar">
                        </div>
                        <div class="message other-message float-right">`+ message.message + `</div>
                    </li>`;
                }
                else {
                    str += ` <li class="clearfix">
                                <div class="message-data">      
                                    <span class="message-data-time">`+ message.time + `</span>
                                </div>
                                <div class="message my-message">`+ message.message + `</div>
                            </li>`;
                }
            }
            document.getElementById("messages").innerHTML = str;
        }
    })
}
