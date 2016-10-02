# Information

## Limitations
- 2 max hdd due to gui limitation
- for correct saving HDD information you need save it in separated dupe
- you can't save full hdd in one dupe more that one bank with advdupe1 or two banks with advdupe2
- file system is very slow without "wire_epxression2_unlimited" or tick/soft quota tweaks

## Sidenotes
- pc noise sound is from css, so without it you don't hear this sound
- there is no scroll in directory listing
- bmp reader always render image as square
- there is no way to restore damaged system

## Known issues
- sometimes e2 chips may crash due to tick quota or unknown function call
- keyboard input have sometimes big delay in multiplayer or not work correct

# Specifications

## Wire Dupable HDD
Bytes in sector: 4 (32bit)  
Max bank size: 1MB  
Max bank count: 4 (can be extended)  

## HDD Controller
Max connected HDDs: 2  

## File System Table
Max Partitions: 4  
Max File System Size: 64MB  
Extended Partition Tables not supported  

## WM1 File System
Cluster size: 32 bytes - 256 KB  
Volume name: 16 chars  
Max file size: 4GB (?)  
Max file name: 12 chars + 3 chars extension, long file names is not supported  
Support file creation/modify/access time as unix stamp.  

## BIOS Error codes (beep)
1 long - OK  
7 short - BIOS ROM error  
1 short with different frequencies - CPU error  
1 long 3 short - GPU error  
1 long 2 short - GPU EGP limits error  
2 long - Keyboard error  
1 very long - Null Byte error  