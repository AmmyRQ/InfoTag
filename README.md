# InfoTag
A simple PocketMine-MP (3.0.0) plugin that allows you to view basic player information in the player's nametag, with PureChat & RankSystem integration (with factions support).

# 👀 First steps
You must first install the plugin ("plugins/" folder) and start the server, and then shut it down.

Inside the "plugin_data/InfoTag" folder a file called "format.yml" will be created, which you can modify.

# ❓ How to modify the format?
You can modify the <a href="https://github.com/AmmyR/InfoTag/blob/main/resources/format.yml#L36">"format"</a> key inside the file <a href="https://github.com/AmmyR/InfoTag/blob/main/resources/format.yml">"format.yml"</a>. 

The following options are available within the available format:

<b>[❗]<i> If PureChat or RankSystem (you can only use one!) are disabled or not installed, the player's nametag will be used instead.</i></b>

<ul>
  <li><b>{nametag}</b> -> PureChat & RankSystem nametag format. </li>
  <li><b>{health}</b> -> Player's current health.</li>
  <li><b>{maxhealth}</b> -> Player's max health.</li>
  <li><b>{food}</b> -> Player's current food.</li>
  <li><b>{maxfood}</b> -> Player's max food.</li>
  <li><b>{ping}</b> -> Player's ping (ms).</li>
  <li><b>{device}</b> -> The device from which the player is playing.</li>
  <li><b>{faction}</b> -> Faction to which the player belongs
</ul>

You can also use the unicode symbols of the game and add colors! (See the <a href="https://github.com/AmmyR/InfoTag/blob/main/resources/format.yml#L31">format.yml</a> file for more information).

# 🎌 Factions
<div id="factions">
There is support for 4 factions plugins:
<ul>
  <li><a href="https://poggit.pmmp.io/p/SimpleFaction/">SimpleFaction</a></li>
  <li><a href="https://poggit.pmmp.io/p/FactionMaster/">FactionMaster</a></li>
  <li><a href="https://poggit.pmmp.io/p/PiggyFactions/">PiggyFactions</a></li>
  <li><a href="https://poggit.pmmp.io/p/FactionsPE/">FactionsPE</a></li>
</ul>
  
You can set which plugin you want to use in the format.yml file, in the <a href="https://github.com/AmmyR/InfoTag/blob/main/resources/format.yml#L16">"factions-plugin"</a> key.

If the plugin of your choice does not exist or is deactivated, the "{faction}" key will simply be ignored and will be displayed without formatting.

<b>[❗] Use plugins provided by Poggit (links above). If you use factions plugins that do not come from this source, an error may occur. Sometimes those who provide plugins from external sources (such as YouTube) modify them.</b>
</div>

# 🔎 Example (Default formatted nametag)
<div align="center">
  <br>
  <img src="example.png"/>
</div>
