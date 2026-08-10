ALX Wiremod E2 PC & OS
=========================  
[![image](https://i.imgur.com/mEg4Fgl.jpg)](https://imgur.com/a/eUHZQ)
More screenshots: https://imgur.com/a/eUHZQ
<br>Video: https://youtu.be/jfFnVnX7Kwg
<br>Video in russian: https://youtu.be/ciM0uYEN5yw

**[Steam Workshop](https://steamcommunity.com/sharedfiles/filedetails/?id=2075107429)**

**Description and wiki is not yet fully updated.**

This is an Wiremod Expression2 OS for Garry's Mod Game.  
Written in in-game Expression2 Language (wiremod addon).  
                                                                     
This is not an real OS, it cannot work anywhere except this game.  
However it works similar to a real PC (boot sequence etc), but still very much simplified.  

### Advanced Engineering Highlights
This project pushes **Expression2** to its absolute limits, implementing complex low-level systems entirely from scratch:

* **Custom Binary Parsers** — Native E2 decoders for **Progressive JPG**, animated **GIF**, **PNG**, **BMP**, and standard **MIDI** files.
* **Multi-Channel MIDI Synthesizer** — Advanced player with 3 playback modes.
* **Low-Level File Systems** — Custom **WM1** and **WCD** storage architectures for virtual HDDs, and CD-ROMs.
* **Modular Component Architecture** — Custom interconnection layer using Expression2 Data Signals to simulate motherboard slots, BIOS, and boot sequence.
* **Network & Router Simulation** — Multi-device routing stack featuring virtual switches, laser transceivers, and a real-world **HTTP gateway**.
* **State Machine Architecture** — Execution splitting across server ticks using finite-state machines to safely handle heavy logic without exceeding E2 Tick Quota and Ops limits.

### [Wiki](https://github.com/AlexALX/wiremod_e2_os/wiki)

Main features:  
* Virtual PC what made of parts.
* Virtual motherboard with pcie/pci sockets and other sockets.
* Virtual CD Drives, HDDs, USB devices, cables, pcie/pci cards, etc.
* Virtual BIOS, boot sequence.
* **Low-level WM1 File System** what works with wire dupable HDDs/EEPROM. 
* **WCD File System** what used for wiremod CD Discs. 
* Expression2 **BMP Reader**, draw on wire Digital Screen (up to 512x512, 1024x1024 Quad Digital Screen). 
* Expression2 Baseline (8/12bits) and Progressive **JPG Reader** for Digital Screen.
* Expression2 **GIF Reader** with animations support for Digital Screen.
* Expression2 **PNG Reader** for Digital Screen.
* **Midi player** with 3 play modes (synth, instrumental, addon).
* Few programs: Console, Burning software, file manager, network chat, partitions manager. 
* Simple directory listing/file managing interface (DOS style).
* Simple text viewer.  
* Simple networking between PC's.
* Satellite internet device, what provides access to real internet (http e2 extension) for files downloading.
* Switch/router network system, also with laser transferer and two-way radio devices.
* Basic **GFX lib** for draw text on Digital Screen.
* ZGPU Image Renderer (E2 -> ZASM bridge).
* Standalone BMP, JPG, GIF, PNG Readers and Midi Player.
* Standalone [Starfall](https://steamcommunity.com/sharedfiles/filedetails/?id=3412004213) BMP Reader.
* And some other features and devices!

Also includes PHP version of WM1 File System.

This addon also included part of **GeneralUser-GS** wav files for midi "addon" playback mode.
https://github.com/mrbumpy409/GeneralUser-GS

### Notes about Midi Player
* May not play all notes simultaneously
* Sound quality for complex midi pretty poor with default e2 limits (check recommended e2 settings)

Created by AlexALX (c) 2016-2026
