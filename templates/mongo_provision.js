var channel_name;
var network_name;

if(channel_name === undefined) {
    channel_name = "#korobi"
}
if(network_name === undefined) {
    network_name = "esper"
}

// Mock stuff
var channel = {
    channel: channel_name,
    command_prefix: ".",
    commands_enabled: true,
    key: null,
    last_activity: new Date(),
    last_valid_content_at: new Date(),
    logs_enabled: true,
    managers: [
        5474071
    ],
    network: network_name,
    permissions: [
        "grant.command.operator.modify",
        "grant.punishment.operator.warn",
        "grant.punishment.operator.warn.normal",
        "grant.punishment.operator.warn.normal.anonymous",
        "grant.punishment.operator.kick",
        "grant.punishment.operator.kick.normal",
        "grant.punishment.operator.kick.normal.anonymous",
        "grant.punishment.operator.ban",
        "grant.punishment.operator.ban.normal",
        "grant.punishment.operator.ban.normal.anonymous"
    ],
    punishments_enabled: true,
    repositories: [
        "korobi.Korobi"
    ],
    topic: {
        actor_host: "$Internal",
        actor_nick: "$Internal",
        time: new Date(),
        value: "\u000310Korobi \u000305| \u000312https://korobi.io | development at https://github.com/korobi/Web \u000305| \u000f@mbaxter | Kashike: Anything else, m'love?"
    }
}

var chats = [{
    actor_hostname: "bendem!bendem@irc.bendem.be",
    actor_name: "bendem",
    actor_prefix: "NORMAL",
    channel: channel_name,
    channel_mode: null,
    date: new Date(),
    message: "",
    network: network_name,
    recipient_hostname: null,
    recipient_name: null,
    recipient_prefix: null,
    type: "JOIN",
    channel_object_id: null
}, {
    actor_hostname: "Borg!Borg@resistance.is.futile.services.esper.net",
    actor_name: "Borg",
    actor_prefix: "OPERATOR",
    channel: channel_name,
    channel_mode: null,
    date: new Date(),
    imported: false,
    message: "+",
    network: network_name,
    recipient_hostname: "bendem!bendem@irc.bendem.be",
    recipient_name: "bendem",
    recipient_prefix: "OPERATOR",
    type: "MODE",
    channel_object_id: null
}, {
    actor_hostname: "kashike!kashike@im.tired.of.this.black.and.blue.kitteh.club",
    actor_name: "kashike",
    actor_prefix: "OPERATOR",
    channel: channel_name,
    channel_mode: null,
    date: new Date(),
    message: "bendem: You're the best",
    network: network_name,
    recipient_hostname: null,
    recipient_name: null,
    recipient_prefix: null,
    type: "MESSAGE",
    channel_object_id: null
}, {
    actor_hostname: "lol768!lol7681@bnc.lol768.com",
    actor_name: "lol768",
    actor_prefix: "OPERATOR",
    channel: channel_name,
    channel_mode: null,
    date: new Date(),
    message: "kashike, Ikr? He really is so cool!",
    network: network_name,
    recipient_hostname: null,
    recipient_name: null,
    recipient_prefix: null,
    type: "MESSAGE",
    channel_object_id: null
}, {
    actor_hostname: "bendem!bendem@irc.bendem.be",
    actor_name: "bendem",
    actor_prefix: "OPERATOR",
    channel: channel_name,
    channel_mode: null,
    date: new Date(),
    message: "Oh thanks, you're all so nice",
    network: network_name,
    recipient_hostname: null,
    recipient_name: null,
    recipient_prefix: null,
    type: "MESSAGE",
    channel_object_id: null
}]

var chat_index = {
    network: network_name,
    channel: channel_name,
    year: new Date().getFullYear(),
    day_of_year: null,
    has_valid_content: true
}

var channel_query = {
    network: network_name,
    channel: channel_name
}

// Insert stuff
if (db.networks.find({"slug": network_name}).count() === 0) {
    db.networks.insert({
        "slug" : network_name,
        "name" : network_name + " Network",
        "network_id" : network_name
    })
}

if(db.channels.find(channel_query).count() === 0) {
    db.channels.insert(channel)
}
var ch = db.channels.findOne(channel_query)
var cid = ch._id

chats.map(function(c) {
    c.channel_object_id = cid
    return c
}).forEach(function(c) {
    db.chats.insert(c)
})

chat_index.day_of_year = db.channels.aggregate([
    { $match: { _id: cid } },
    { $project: { doy: { $dayOfYear: "$last_activity" } } }
]).toArray()[0].doy

if(db.chat_indexes.find(chat_index).count() === 0) {
    db.chat_indexes.insert(chat_index)
}
