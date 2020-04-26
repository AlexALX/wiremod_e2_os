//if (AlxPcInited!=nil) then return end -- prevent calling this file twice

-- Fix for file.* functions, needed for advdupe2, finding lua files from dupes on workshop.
local file_Open = file.Open
local file_Find = file.Find
local file_Exists = file.Exists
local file_CreateDir = file.CreateDir

-- fixing advdupe\* command
if (!file_Exists("advdupe2/alx_pc_readonly/","DATA")) then
	file_CreateDir("advdupe2/alx_pc_readonly/","DATA");
end

-- needed for detect when this lib loaded or not in other files.
function AlxPcInited()
	return true;
end

-- need keep old way for compatibility with other mods (including wiremod)
function file.Open(path,mode,param)
	local dir = "GAME";
	if (not param) then
		dir = "DATA"
	elseif (param==true) then
		dir = "GAME";
	else
		dir = param
	end
	if (dir:upper()=="DATA") then
		-- read mode
		if (mode=="r" or mode=="rb") then
			-- special workaround for workshop and advdupe2
			if path and path:lower():find("^advdupe2/alx_pc_readonly/(.*).txt$") then
				return file_Open("lua/data/"..string.Replace(path,".txt",".lua"),mode,"GAME");
			end
		-- forbid write mode
		else
			if path and path:lower():find("^advdupe2/alx_pc_readonly/") then
				return false;
			end
		end
	end
	return file_Open(path,mode,dir);
end

function file.CreateDir(path,param)
	local dir = "GAME";
	if (not param) then
		dir = "DATA"
	elseif (param==true) then
		dir = "GAME";
	else
		dir = param
	end
	if (dir:upper()=="DATA") then
		-- disable write in readonly dir
		if path and path:lower():find("^advdupe2/alx_pc_readonly/") then
			return false;
		end
	end
	return file_CreateDir(path,dir);
end

function file.Exists(path,param)
	local dir = "GAME";
	if (not param) then
		dir = "DATA"
	elseif (param==true) then
		dir = "GAME";
	else
		dir = param
	end
	if (dir:upper()=="DATA" and not file_Exists(path,dir)) then
		-- special workaround for workshop and advdupe2
		if path and path:lower():find("^advdupe2/alx_pc_readonly/(.*).txt$") then
			return file_Exists("lua/data/"..string.Replace(path,".txt",".lua"),"GAME");
		end
	end
	return file_Exists(path,dir);
end

function file.Find(path,dir,order)
	if (path==nil or dir==nil) then return {},{} end
	if (dir:upper()=="DATA") then
		local files,folders = file_Find(path,dir,order);
		-- ugly workaround for workshop...
		if path and (path:lower():find("^advdupe2/alx_pc_readonly/(.*)$")
		or path:lower():find("^advdupe2/alx_pc_readonly/(.*)$")) then
			local fi,fo = file_Find("lua/data/"..path,"GAME");
            if (fi) then
	 			for k,d in pairs(fi) do
					if (not table.HasValue(files,d)) then
						table.insert(files,d);
					end
				end
            end
            if (fo) then
	 			for k,d in pairs(fo) do
					if (not table.HasValue(folders,d)) then
						table.insert(folders,d);
					end
				end
            end
		end
		-- i know, order will not work correct in this case, later will fix probably
		return files,folders;
	else
		return file_Find(path,dir,order);
	end
end