# ALX Wiremod E2 PC & OS
[![image](https://i.imgur.com/mEg4Fgl.jpg)](https://imgur.com/a/eUHZQ)
More screenshots: https://imgur.com/a/eUHZQ
<br>Video: https://youtu.be/jfFnVnX7Kwg
<br>Video in russian: https://youtu.be/ciM0uYEN5yw

**[Steam Workshop](https://steamcommunity.com/sharedfiles/filedetails/?id=2075107429)**

**Documentation:** [ALX PC Wiki](https://github.com/AlexALX/wiremod_e2_os/wiki)

## Overview
A virtual computer and custom operating system built entirely within the **Wiremod Expression 2** environment for **Garry's Mod** game.  
<br>**Note:** This is an in-game sandbox project and not a real-world OS for physical hardware.

----

Build your own virtual PC from individual components, boot it, manage files, run programs, view images from the Internet, play MIDI files, and connect your PCs together over the virtual network.

ALX PC is a fully functional computer built entirely in Wiremod Expression 2.

It provides its own simplified computer architecture with modular hardware, BIOS, storage, filesystem, networking, graphics, multimedia and applications. Everything is implemented in Expression 2 and designed specifically for the Wiremod environment.

The addon also includes standalone image readers for BMP, JPEG, GIF and PNG, a MIDI player, and a Starfall BMP Reader.

### Advanced Engineering Highlights
This project pushes **Expression2** to its absolute limits, implementing complex low-level systems entirely from scratch:

* **Custom Binary Parsers** — Native E2 decoders for **Progressive JPG**, animated **GIF**, **PNG**, **BMP**, and standard **MIDI** files.
* **Multi-Channel MIDI Synthesizer** — Advanced player with 3 playback modes.
* **Low-Level File Systems** — Custom **WM1** and **WCD** storage architectures for virtual HDDs, and CD-ROMs.
* **Modular Component Architecture** — Custom interconnection layer using Expression2 Data Signals to simulate motherboard slots, BIOS, and boot sequence.
* **Network & Router Simulation** — Multi-device routing stack featuring virtual switches, laser transceivers, and a real-world **HTTP gateway**.
* **State Machine Architecture** — Execution splitting across server ticks using finite-state machines to safely handle heavy logic without exceeding E2 Tick Quota and Ops limits.

### Main features
* **Virtual PC** assembled from individual components.
* **Virtual motherboard** with PCIe, PCI and other expansion sockets.
* **Virtual hardware** including CD drives, HDDs, USB devices, cables and PCIe/PCI cards.
* **Virtual BIOS** with a complete boot sequence.
* **WM1 File System** — low-level filesystem designed for wire DHDDs and EEPROMs.
* **WCD File System** — filesystem used for Wiremod CD discs.
* **CHIP-8 Emulator** — supports CHIP-8, Super-CHIP, and XO-CHIP specifications with dual resolution modes (up to 128×64), 4-color multi-plane graphics, hardware scrolling, dynamic audio pitch shifting, and local multiplayer.
* **E2 BMP Reader** — outputs images to a Digital Screen up to 512×512 or a Quad Digital Screen up to 1024×1024.
* **E2 JPEG Reader** — supports Baseline and Progressive JPEG.
* **E2 GIF Reader** — supports animated GIFs.
* **E2 PNG Reader** — for loading PNG images.
* **MIDI Player** with three playback modes: Synth, Instrumental and Addon.
* **E2 ZIP Reader** for ALX PC.
* **Built-in programs** including Console, Burning Software, File Manager, Network Chat and Partition Manager.
* **Console** with support for drawing on Wiremod Console Screens and Digital Screens.
* **Mini-games** including Flappy Bird, Pong and Snake.
* **ALX OS E2 Executables** — runs custom .e2z, .e2p and .e2r executable formats on the Remote Executor PCI board using the RemoteUpload extension.
* **DOS-style file management interface** for directory listing and file management.
* **Text Viewer** for viewing text files.
* **Networking** for communication between ALX PCs.
* **Satellite Internet device** providing access to the real Internet through the E2 HTTP extension, including file downloading.
* **Network devices** including switches, routers, laser transceivers and two-way radio devices.
* **Basic GFX library** for rendering text on Digital Screens.
* **ZGPU Image Renderer** providing an E2 → ZASM graphics bridge.
* **Standalone image readers** for BMP, JPEG, GIF and PNG.
* **Standalone MIDI player**.
* **Standalone Starfall BMP Reader**.
* **Standalone CHIP-8 Emulator**.
* **Doom game prototype** (not included in workshop) - just map level loading and player movement.
* And some other features and devices.

Also includes PHP version of the WM1 File System.

### GeneralUser-GS Note
This addon also includes part of **GeneralUser-GS** .wav files for midi "addon" playback mode.  
https://github.com/mrbumpy409/GeneralUser-GS

### Notes about Midi Player
* May not play all notes simultaneously.
* Sound quality for complex midi pretty poor with default E2 limits (check [Recommended Settings](https://github.com/AlexALX/wiremod_e2_os/wiki/Recommended-Settings#midi-player)).

Created by AlexALX (c) 2016-2026
