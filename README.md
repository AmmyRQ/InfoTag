# InfoTag
A simple PocketMine-MP (3.0.0) plugin that allows you to view basic player information in the player's nametag, with PureChat integration.

# 👀 First steps
You must first install the plugin ("plugins/" folder) and start the server, and then shut it down.

Inside the "plugin_data/InfoTag" folder a file called "format.yml" will be created, which you can modify.

# ❓ How to modify the format?
You can modify the "format" key inside the file <a href="resources/format.yml">"format.yml"</a>. 

The following options are available within the available format:
<ul>
  <li><b>{nametag}</b> -> PureChat nametag format. <b><i>[❗] If PureChat is disabled or not installed, the player's nametag will be used instead.</i></b></li>
  <li><b>{health}</b> -> Player's current health.</li>
  <li><b>{maxhealth}</b> -> PLayer's max health.</li>
  <li><b>{food}</b> -> Player's current food.</li>
  <li><b>{maxfood}</b> -> Player's max food.</li>
  <li><b>{ping}</b> -> Player's ping (ms).</li>
  <li><b>{device}</b> -> The device from which the player is playing.</li>
</ul>

You can also use the unicode symbols of the game (https://wiki.bedrock.dev/concepts/emojis.html) and add colors! (See the <a href="resources/format.yml">format.yml</a> file for more information).

# 🔎 Example (Default formatted nametag)
<img src="example.png"/>
