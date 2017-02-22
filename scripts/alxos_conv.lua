/*
	Convert ALX E2 OS Downloaded file to binary
	Created by AlexALX
*/

local function byte2strraw(Num) 
    local Ret = ""
    for K=0,3 do
        local Tmp = bit.band(bit.rshift(Num,(8*K)),0xFF)
        local Str = string.char(Tmp)
		Ret = Ret..Str
    end
    return Ret
end

local function nicesize(Sz)
    local SzS = Sz.." Bytes"
    if (Sz/1024>1) then
        SzS = math.floor(Sz/1024,2).." KB"
    end
    return SzS
end

function alxos_convert(File)
	File = File:Replace(".txt","")
	local F = file.Read("e2files/"..File..".txt")
    if (F!="") then
		local JSON = util.JSONToTable(F)
		if (not JSON or table.Count(JSON)<2) then
			print("Error with parsing file!")
			return
		end
		local cont = ""
		
		print("Starting conversion, this can take few minutes...")
		
		for k,v in pairs(JSON[2]) do
			cont = cont..byte2strraw(tonumber(v))
		end
		
		cont = cont:sub(0,JSON[1]["size"])
		
		file.Write("e2files/"..File.."_conv.txt",cont) 
		print("Conversion done!")
		print("File size: "..nicesize(JSON[1]["size"]))
		print("File name: "..JSON[1]["name"])
		print("File ext: "..JSON[1]["ext"])
		print("Don't forget change file extension from .txt to ."..JSON[1]["ext"])
	else
		print("File not exists or empty!")
	end
end