ALX_PC = ALX_PC or {}

if CLIENT then
    local basePath = "lua/data/advdupe2/alx_pc_readonly"

    ALX_PC.dupes = {}

    -- Formats raw file names into human-readable titles (e.g. "hdmi_cable" -> "Hdmi Cable")
    local function FormatNiceName(fileName)
        local name = string.StripExtension(fileName):gsub("_", " ")
        return (name:gsub("(%a)([%w_']*)", function(first, rest)
            return first:upper() .. rest:lower()
        end))
    end

    -- Recursively scans directories to build the dupe hierarchy
    local function ScanDupeDirectory(currentPath, targetTable, initialTable, parentCategory)
        local files, folders = file.Find(currentPath .. "/*", "GAME")

        if files then
            for _, fileName in ipairs(files) do
                if string.EndsWith(fileName, ".lua") then
                    local relativePath = currentPath:sub(#basePath + 2) .. "/" .. string.StripExtension(fileName)
                    if relativePath:sub(1, 1) == "/" then relativePath = relativePath:sub(2) end

                    local Info = {
                        name = FormatNiceName(fileName),
                        file = relativePath
                    }

                    local IconPath = "alxpc_icons/"..relativePath..".png"
                    if file.Exists("materials/"..IconPath, "GAME") then
                        Info.icon = IconPath
                    end

                    table.insert(targetTable, Info)
                end
            end
        end

        if folders then
            for _, folderName in ipairs(folders) do
                local categoryName = FormatNiceName(folderName)
                if parentCategory!="" then
                    categoryName = parentCategory .. " - " .. categoryName
                end
                initialTable[categoryName] = initialTable[categoryName] or {}

                ScanDupeDirectory(currentPath .. "/" .. folderName, initialTable[categoryName], initialTable, categoryName)
            end
        end

        if table.Count(targetTable)!=0 then
            targetTable["Automatic Assembler"] = {
                {
                    name = "PC in Case",
                    file = "alx_pc_assembler_case",
                    icon = "alxpc_icons/alx_pc_assembler_case.png",
                    no_menu = true
                },
                {
                    name = "Minimum Setup",
                    file = "alx_pc_assembler_min",
                    icon = "alxpc_icons/alx_pc_assembler_min.png",
                    no_menu = true
                },
            }
        end
    end

    ScanDupeDirectory(basePath, ALX_PC.dupes, ALX_PC.dupes, "")

    local function spawnUsingAdvdupe2(obj)
        RunConsoleCommand("gmod_tool", "advdupe2")

        -- 2. Read file raw contents from lua/data/
        local filePath = basePath .. "/" .. obj.file .. ".lua"
        local fileContent = file.Read(filePath, "GAME")

        if fileContent and fileContent ~= "" then
            local name = obj.file:match("([^/]+)$") or obj.file
            local read = fileContent

            local success, dupe, info, moreinfo = AdvDupe2.Decode(read)
            if(success)then
                AdvDupe2.SendFile(name, read)

                AdvDupe2.LoadGhosts(dupe, info, moreinfo, name)
            else
                AdvDupe2.Notify("File could not be decoded. ("..dupe..") Upload Canceled.", NOTIFY_ERROR)
            end

        else
            AdvDupe2.Notify("Failed to open file in AdvDupe2: " .. obj.file, NOTIFY_ERROR)
        end
    end

    -- Displays a stylized dark confirmation dialog for Automatic Assembler
    local function OpenAssemblerWarning(fileSpawnName)
        local frame = vgui.Create("DFrame")
        frame:SetTitle("")
        frame:SetSize(540, 310)
        frame:Center()
        frame:MakePopup()
        frame:DoModal(true)
        frame:ShowCloseButton(false)

        -- Modern dark frame styling
        frame.Paint = function(self, w, h)
            draw.RoundedBox(8, 0, 0, w, h, Color(30, 33, 40, 250))
            draw.RoundedBox(8, 1, 1, w - 2, h - 2, Color(42, 46, 57, 255))

            -- Header bar
            draw.RoundedBoxEx(8, 1, 1, w - 2, 36, Color(52, 58, 70, 255), true, true, false, false)

            -- Header icon and text
            surface.SetDrawColor(255, 255, 255, 255)
            surface.SetMaterial(Material("icon16/cog.png"))
            surface.DrawTexturedRect(12, 10, 16, 16)

            draw.SimpleText("Automatic Assembler Warning", "DermaDefaultBold", 36, 11, Color(230, 235, 240), TEXT_ALIGN_LEFT, TEXT_ALIGN_TOP)
        end

        -- Custom close button
        local closeBtn = vgui.Create("DButton", frame)
        closeBtn:SetText("✕")
        closeBtn:SetSize(30, 26)
        closeBtn:SetPos(frame:GetWide() - 36, 5)
        closeBtn:SetTextColor(Color(180, 190, 200))
        closeBtn:SetFont("DermaDefaultBold")
        closeBtn.Paint = function(self, w, h)
            if self:IsHovered() then
                draw.RoundedBox(4, 0, 0, w, h, Color(220, 60, 60, 200))
            end
        end
        closeBtn.DoClick = function() frame:Close() end

        -- Text panel container
        local infoPanel = vgui.Create("DPanel", frame)
        infoPanel:Dock(TOP)
        infoPanel:DockMargin(15, 25, 15, 5)
        infoPanel:SetTall(200)
        infoPanel.Paint = function() end

        local infoText = vgui.Create("DLabel", infoPanel)
        infoText:Dock(TOP)
        infoText:SetFont("DermaDefaultBold")
        infoText:SetTextColor(Color(220, 225, 230))
        infoText:SetWrap(true)
        infoText:SetAutoStretchVertical(true)
        infoText:SetText(
            "This tool will build a complete PC for you automatically, but you'll miss out on the true drive and satisfaction of assembling your custom setup piece-by-piece!\n\n" ..
            "I highly recommend trying manual assembly first. Check out the building guide here:"
        )

        -- Custom Clickable Link with updated Wiki URL
        local wikiURL = "https://github.com/AlexALX/wiremod_e2_os/wiki/Assembling-ALX-PC"
        local linkLabel = vgui.Create("DLabel", infoPanel)
        linkLabel:Dock(TOP)
        linkLabel:DockMargin(0, 4, 0, 8)
        linkLabel:SetFont("DermaDefaultBold")
        linkLabel:SetTextColor(Color(90, 170, 255))
        linkLabel:SetText(wikiURL)
        linkLabel:SetMouseInputEnabled(true)
        linkLabel:SetCursor("hand")
        linkLabel.DoClick = function()
            gui.OpenURL(wikiURL)
        end

        -- Additional notice about BIOS / OS setup (using matching DermaDefaultBold font)
        local noteText = vgui.Create("DLabel", infoPanel)
        noteText:Dock(TOP)
        noteText:DockMargin(0, 4, 0, 0)
        noteText:SetFont("DermaDefaultBold")
        noteText:SetTextColor(Color(190, 195, 205))
        noteText:SetWrap(true)
        noteText:SetAutoStretchVertical(true)
        noteText:SetText(
            "Note: Even after auto-assembly, you will still need to:\n" ..
            "1. Spawn an ALX OS setup disc and insert it into the CD drive.\n" ..
            "2. Enter BIOS settings and change the boot device.\n" ..
            "3. Install the OS itself.\n\n" ..
            "Important: After spawning, wait until all components and cables are connected and the clicking sounds stop!"
        )

        -- Bottom button panel
        local btnPanel = vgui.Create("DPanel", frame)
        btnPanel:Dock(BOTTOM)
        btnPanel:DockMargin(15, 0, 15, 15)
        btnPanel:SetTall(38)
        btnPanel.Paint = function() end

        -- Button 1: Cancel / Build Manually (Primary - Green)
        local btnCancel = vgui.Create("DButton", btnPanel)
        btnCancel:SetText("")
        btnCancel:Dock(LEFT)
        btnCancel:SetWide(235)

        local iconWrench = Material("icon16/wrench.png")
        btnCancel.Paint = function(self, w, h)
            local bgColor = self:IsHovered() and Color(46, 160, 85) or Color(38, 135, 72)
            draw.RoundedBox(6, 0, 0, w, h, bgColor)

            -- Centered calculation for icon + text group
            surface.SetFont("DermaDefaultBold")
            local textW = surface.GetTextSize("I'll build it manually!")
            local startX = (w - (16 + 8 + textW)) / 2

            surface.SetDrawColor(255, 255, 255, 255)
            surface.SetMaterial(iconWrench)
            surface.DrawTexturedRect(startX, (h - 16) / 2, 16, 16)

            draw.SimpleText("I'll build it manually!", "DermaDefaultBold", startX + 24, h / 3, Color(255, 255, 255), TEXT_ALIGN_LEFT, TEXT_ALIGN_VCENTER)
        end
        btnCancel.DoClick = function()
            frame:Close()
        end

        -- Button 2: Confirm Auto-Assembly (Secondary - Red)
        local btnConfirm = vgui.Create("DButton", btnPanel)
        btnConfirm:SetText("")
        btnConfirm:Dock(RIGHT)
        btnConfirm:SetWide(235)

        local iconLightning = Material("icon16/lightning.png")
        btnConfirm.Paint = function(self, w, h)
            local bgColor = self:IsHovered() and Color(190, 50, 50) or Color(150, 40, 40)
            draw.RoundedBox(6, 0, 0, w, h, bgColor)

            -- Centered calculation for icon + text group
            surface.SetFont("DermaDefaultBold")
            local textW = surface.GetTextSize("Assemble it for lazy me!")
            local startX = (w - (16 + 8 + textW)) / 2

            surface.SetDrawColor(255, 255, 255, 255)
            surface.SetMaterial(iconLightning)
            surface.DrawTexturedRect(startX, (h - 16) / 2, 16, 16)

            draw.SimpleText("Assemble it for lazy me!", "DermaDefaultBold", startX + 24, h / 3, Color(255, 255, 255), TEXT_ALIGN_LEFT, TEXT_ALIGN_VCENTER)
        end
        btnConfirm.DoClick = function()
            net.Start("ALX_PC_SpawnDupe")
                net.WriteString(fileSpawnName)
            net.SendToServer()
            frame:Close()
        end
    end

    spawnmenu.AddContentType("alxpc_dupe", function(container, obj)
        local icon = vgui.Create("ContentIcon", container)
        icon:SetContentType("alx_native_dupe")
        icon:SetSpawnName(obj.file)
        icon:SetName(obj.name)
        icon:SetMaterial(obj.icon or "icon16/page.png")

        icon.DoClick = function()
            surface.PlaySound("ui/buttonclickrelease.wav")

            if obj.file == "alx_pc_assembler_case" or obj.file == "alx_pc_assembler_min" then
                OpenAssemblerWarning(obj.file)
            else
                net.Start("ALX_PC_SpawnDupe")
                    net.WriteString(obj.file)
                net.SendToServer()
            end
        end

        if obj.no_menu == nil or not obj.no_menu then
            icon.OpenMenu = function(self)
                local menu = DermaMenu()
                local option = menu:AddOption("Spawn using AdvDupe2", function()
                    spawnUsingAdvdupe2(obj)
                end)
                option:SetImage("icon16/brick_add.png")
                menu:Open()
            end
        end

        if IsValid(container) then
            container:Add(icon)
        end

        return icon
    end)

    hook.Add("PopulateEntities", "ALXPC_InjectCategories", function(pnlContent, tree, nodeRoot)
        local hasWiremod = WireLib ~= nil
        local hasAdvDupe2 = AdvDupe2 ~= nil

        local mainCategoryNode = tree:AddNode("ALX E2 Wiremod PC", "icon16/drive.png")
        local containerPanel = vgui.Create("ContentContainer", pnlContent)
        containerPanel:SetVisible(false)
        containerPanel:SetTriggerSpawnlistChange(false)

        mainCategoryNode.DoClick = function()
            pnlContent:SwitchPanel(containerPanel)
        end

        local githubNode = mainCategoryNode:AddNode("Github", "icon16/page_white_code.png")
        githubNode.DoClick = function()
            gui.OpenURL("https://github.com/AlexALX/wiremod_e2_os")
        end

        local wikiNode = mainCategoryNode:AddNode("Wiki", "icon16/page_white_text.png")
        wikiNode.DoClick = function()
            gui.OpenURL("https://github.com/AlexALX/wiremod_e2_os/wiki")
        end

        local recNode = mainCategoryNode:AddNode("Recommended Settings", "icon16/cog.png")
        recNode.DoClick = function()
            gui.OpenURL("https://github.com/AlexALX/wiremod_e2_os/wiki/Recommended-Settings")
        end

        timer.Simple(0, function()
            if IsValid(mainCategoryNode) then
                mainCategoryNode:SetExpanded(false)
            end
        end)

        if not hasWiremod or not hasAdvDupe2 then
            local warnBanner = vgui.Create("DPanel")

            local height = 90
            if not hasWiremod and not hasAdvDupe2 then
                height = 110
            end

            warnBanner:SetTall(height)
            warnBanner.Paint = function(self, w, h)
                draw.RoundedBox(6, 0, 0, w, h, Color(45, 30, 30, 240))
                draw.RoundedBox(6, 1, 1, w - 2, h - 2, Color(65, 38, 38, 255))
                draw.RoundedBoxEx(6, 1, 1, 5, h - 2, Color(220, 60, 60), true, false, true, false)
            end

            local warnLabel = vgui.Create("DLabel", warnBanner)
            warnLabel:Dock(TOP)
            warnLabel:DockMargin(15, 8, 10, 4)
            warnLabel:SetFont("DermaDefaultBold")
            warnLabel:SetTextColor(Color(255, 200, 200))
            warnLabel:SetWrap(true)
            warnLabel:SetAutoStretchVertical(true)

            local missingText = "Warning: Required addons are missing! ALX PC requires both Wiremod and AdvDupe2 to function.\n\nPlease install:"
            warnLabel:SetText(missingText)

            if not hasWiremod then
                local wireBtn = vgui.Create("DLabel", warnBanner)
                wireBtn:Dock(TOP)
                wireBtn:DockMargin(15, 2, 10, 2)
                wireBtn:SetFont("DermaDefaultBold")
                wireBtn:SetTextColor(Color(100, 180, 255))
                wireBtn:SetText("• Click here to open Wiremod Workshop page")
                wireBtn:SetMouseInputEnabled(true)
                wireBtn:SetCursor("hand")
                wireBtn.DoClick = function()
                    gui.OpenURL("https://steamcommunity.com/sharedfiles/filedetails/?id=160250458")
                end
            end

            if not hasAdvDupe2 then
                local dupeBtn = vgui.Create("DLabel", warnBanner)
                dupeBtn:Dock(TOP)
                dupeBtn:DockMargin(15, 2, 10, 2)
                dupeBtn:SetFont("DermaDefaultBold")
                dupeBtn:SetTextColor(Color(100, 180, 255))
                dupeBtn:SetText("• Click here to open Advanced Duplicator 2 Workshop page")
                dupeBtn:SetMouseInputEnabled(true)
                dupeBtn:SetCursor("hand")
                dupeBtn.DoClick = function()
                    gui.OpenURL("https://steamcommunity.com/sharedfiles/filedetails/?id=773402917")
                end
            end

            local oldLayout = containerPanel.PerformLayout
            containerPanel.PerformLayout = function(self, w, h)
                if IsValid(warnBanner) then
                    warnBanner:SetWide(w - 12)
                end
                if oldLayout then oldLayout(self, w, h) end
            end

            containerPanel:Add(warnBanner)
            return
        end

        local infoBanner = vgui.Create("DPanel")
        infoBanner:SetTall(54)
        infoBanner.Paint = function(self, w, h)
            draw.RoundedBox(6, 0, 0, w, h, Color(35, 38, 45, 240))
            draw.RoundedBox(6, 1, 1, w - 2, h - 2, Color(48, 52, 63, 255))
            draw.RoundedBoxEx(6, 1, 1, 5, h - 2, Color(60, 140, 230), true, false, true, false)
        end

        local infoLabel = vgui.Create("DLabel", infoBanner)
        infoLabel:Dock(FILL)
        infoLabel:DockMargin(15, 5, 10, 5)
        infoLabel:SetFont("DermaDefaultBold")
        infoLabel:SetTextColor(Color(220, 225, 230))
        infoLabel:SetWrap(true)
        infoLabel:SetAutoStretchVertical(true)
        infoLabel:SetText(
            "Notice: All items here are just AdvDupe2 duplicates, located in the Advanced Duplicator 2 tool inside the 'alx_pc_readonly' folder.\n\n" ..
            "Note: Feel free to check out the Automatic Assembler if you want to see an automated build process, but building the PC yourself piece-by-piece is where the real fun lies!"
        )

        local lastH = 0
        infoBanner.Think = function(self)
            if not IsValid(infoLabel) then return end

            local targetH = infoLabel:GetTall() + 10
            if lastH ~= targetH then
                lastH = targetH
                self:SetTall(targetH)
                if IsValid(containerPanel.IconLayout) then
                    containerPanel.IconLayout:InvalidateLayout()
                end
            end
        end

        local oldLayout = containerPanel.PerformLayout
        containerPanel.PerformLayout = function(self, w, h)
            if IsValid(infoBanner) then
                local targetW = w - 24
                if targetW > 50 and infoBanner:GetWide() ~= targetW then
                    infoBanner:SetWide(targetW)
                end
            end
            if oldLayout then oldLayout(self, w, h) end
        end

        containerPanel:Add(infoBanner)

        for categoryName, itemsList in SortedPairs(ALX_PC.dupes) do
            local headerLabel = vgui.Create("ContentHeader", containerPanel)
            headerLabel:SetText(categoryName)
            containerPanel:Add(headerLabel)

            for _, dupeItem in ipairs(itemsList) do
                spawnmenu.CreateContentIcon("alxpc_dupe", containerPanel, {
                    type = "alxpc_dupe",
                    name = dupeItem.name,
                    file = dupeItem.file,
                    icon = dupeItem.icon,
                    no_menu = dupeItem.no_menu,
                })
            end
        end
    end)

    -- Net receiver for handling server warnings regarding low socket limits
    net.Receive("ALX_PC_SpawnDupe", function(len, ply)
        local frame = vgui.Create("DFrame")
        frame:SetTitle("")
        frame:SetSize(540, 230)
        frame:Center()
        frame:MakePopup()
        frame:DoModal(true)
        frame:ShowCloseButton(false)

        -- Modern dark frame styling matching the assembler dialog
        frame.Paint = function(self, w, h)
            draw.RoundedBox(8, 0, 0, w, h, Color(30, 33, 40, 250))
            draw.RoundedBox(8, 1, 1, w - 2, h - 2, Color(42, 46, 57, 255))

            -- Header bar
            draw.RoundedBoxEx(8, 1, 1, w - 2, 36, Color(52, 58, 70, 255), true, true, false, false)

            -- Header icon and text
            surface.SetDrawColor(255, 255, 255, 255)
            surface.SetMaterial(Material("icon16/error.png"))
            surface.DrawTexturedRect(12, 10, 16, 16)

            draw.SimpleText("Low Wire Sockets Limit Warning", "DermaDefaultBold", 36, 11, Color(240, 210, 100), TEXT_ALIGN_LEFT, TEXT_ALIGN_TOP)
        end

        -- Custom close button
        local closeBtn = vgui.Create("DButton", frame)
        closeBtn:SetText("✕")
        closeBtn:SetSize(30, 26)
        closeBtn:SetPos(frame:GetWide() - 36, 5)
        closeBtn:SetTextColor(Color(180, 190, 200))
        closeBtn:SetFont("DermaDefaultBold")
        closeBtn.Paint = function(self, w, h)
            if self:IsHovered() then
                draw.RoundedBox(4, 0, 0, w, h, Color(220, 60, 60, 200))
            end
        end
        closeBtn.DoClick = function() frame:Close() end

        -- Text panel container
        local infoPanel = vgui.Create("DPanel", frame)
        infoPanel:Dock(TOP)
        infoPanel:DockMargin(15, 25, 15, 5)
        infoPanel:SetTall(150)
        infoPanel.Paint = function() end

        -- Main warning description
        local infoText = vgui.Create("DLabel", infoPanel)
        infoText:Dock(TOP)
        infoText:SetFont("DermaDefaultBold")
        infoText:SetTextColor(Color(220, 225, 230))
        infoText:SetWrap(true)
        infoText:SetAutoStretchVertical(true)
        infoText:SetText(
            "The sbox_maxwire_sockets server variable is less than 50, so the PC in Case setup will not function at all.\n\n" ..
            "You need to increase the socket limit or use the Minimum Setup option instead.\n\n" ..
            "Check recommended server settings here:"
        )

        -- Recommended settings URL link
        local recURL = "https://github.com/AlexALX/wiremod_e2_os/wiki/Recommended-Settings"
        local linkLabel = vgui.Create("DLabel", infoPanel)
        linkLabel:Dock(TOP)
        linkLabel:DockMargin(0, 6, 0, 0)
        linkLabel:SetFont("DermaDefaultBold")
        linkLabel:SetTextColor(Color(90, 170, 255))
        linkLabel:SetText(recURL)
        linkLabel:SetMouseInputEnabled(true)
        linkLabel:SetCursor("hand")
        linkLabel.DoClick = function()
            gui.OpenURL(recURL)
        end

        -- Bottom button panel
        local btnPanel = vgui.Create("DPanel", frame)
        btnPanel:Dock(BOTTOM)
        btnPanel:DockMargin(15, 0, 15, 15)
        btnPanel:SetTall(38)
        btnPanel.Paint = function() end

        -- Single confirmation button ("I understand")
        local btnUnderstand = vgui.Create("DButton", btnPanel)
        btnUnderstand:SetText("")
        btnUnderstand:Dock(FILL)

        local iconCheck = Material("icon16/tick.png")
        btnUnderstand.Paint = function(self, w, h)
            local bgColor = self:IsHovered() and Color(60, 140, 230) or Color(45, 115, 190)
            draw.RoundedBox(6, 0, 0, w, h, bgColor)

            -- Centered calculation for icon + text group
            surface.SetFont("DermaDefaultBold")
            local textW = surface.GetTextSize("I Understand")
            local startX = (w - (16 + 8 + textW)) / 2

            surface.SetDrawColor(255, 255, 255, 255)
            surface.SetMaterial(iconCheck)
            surface.DrawTexturedRect(startX, (h - 16) / 2, 16, 16)

            draw.SimpleText("I Understand", "DermaDefaultBold", startX + 24, h / 3, Color(255, 255, 255), TEXT_ALIGN_LEFT, TEXT_ALIGN_VCENTER)
        end
        btnUnderstand.DoClick = function()
            frame:Close()
        end
    end)
end

if SERVER then
    AddCSLuaFile()
    util.AddNetworkString("ALX_PC_SpawnDupe")

    local basePath = "lua/data/advdupe2/alx_pc_readonly"

    --[[
        SpawnALXDupe
        Reads, decodes, and queues an ALX preset dupe for pasting without overriding active tool state.
    --]]
    function ALX_PC.SpawnALXDupe(ply, dupeFile, spawnPos, spawnAng, preSpawnHook)
        if not IsValid(ply) then return false end

        spawnPos = spawnPos or ply:GetEyeTrace().HitPos
        spawnAng = spawnAng or Angle(0, ply:EyeAngles().y + 180, 0)

        local filePath = basePath .. "/" .. dupeFile .. ".lua"
        if not file.Exists(filePath, "GAME") then
            AdvDupe2.Notify(ply, "ALX Dupe file not found: " .. dupeFile, NOTIFY_ERROR)
            return false
        end

        local fileContent = file.Read(filePath, "GAME")
        if not fileContent or fileContent == "" then
            AdvDupe2.Notify(ply, "Failed to read dupe file: " .. dupeFile, NOTIFY_ERROR)
            return false
        end

        local success, dupeTable = AdvDupe2.Decode(fileContent)
        if not success or not dupeTable or not dupeTable.Entities then
            AdvDupe2.Notify(ply, "Failed to decode ALX dupe: " .. dupeFile, NOTIFY_ERROR)
            return false
        end

        if preSpawnHook then
            preSpawnHook(dupeTable)
        end

        -- Calculate elevation offset relative to the surface
        local headEnt = dupeTable.HeadEnt
        if headEnt and headEnt.Pos then
            if not headEnt.Z then
                local tr = util.TraceLine({
                    mask = MASK_NPCWORLDSTATIC,
                    start = headEnt.Pos + Vector(0, 0, 1),
                    endpos = headEnt.Pos - Vector(0, 0, 50000)
                })
                headEnt.Z = tr.Hit and math.abs(headEnt.Pos.Z - tr.HitPos.Z) or 0
            end

            spawnPos.z = spawnPos.z + headEnt.Z
        end

        -- Temporarily back up original dupe data to avoid corrupting active tool selection
        local oldDupe = ply.AdvDupe2

        local dupeName = dupeFile:match("([^/]+)$") or dupeFile

        ply.AdvDupe2 = {
            Entities = dupeTable.Entities,
            Constraints = dupeTable.Constraints,
            HeadEnt = dupeTable.HeadEnt,
            Revision = dupeTable.Revision or AdvDupe2.CodecRevision,
            Pasting = true,
            Name = dupeName
        }

        -- Queue paste operation (InitPastingQueue immediately copies ply.AdvDupe2.Entities)
        AdvDupe2.InitPastingQueue(
            ply,
            spawnPos,
            spawnAng,
            nil,
            true,
            true,
            false,
            true
        )

        local i = #AdvDupe2.JobManager.Queue
        AdvDupe2.JobManager.Queue[i].custom_options = dupeTable.custom_options
        AdvDupe2.JobManager.Queue[i].dupe_name = dupeName

        -- Restore original dupe state
        ply.AdvDupe2 = oldDupe

        return true
    end

    hook.Add("AdvDupe_FinishPasting", "ALXPC_AdvDupeFinish", function(info)
        local data = info[1]
        local ply = data.Player

        if not IsValid(ply) then return end

        local matchedOptions = nil
        local matchedQueue = nil

        for _, queue in ipairs(AdvDupe2.JobManager.Queue) do
            if queue.Player == ply and queue.CreatedEntities == data.CreatedEntities then
                matchedQueue = queue
                break
            end
        end

        if matchedQueue then
            if ALX_PC.stepIndex>0 then
                for k, v in pairs(info[1].CreatedEntities) do
                    table.insert(ALX_PC.AllEntities, v)
                end

                ALX_PC.NextStep(ply)
            end
        end

        local unfreeze_except = ""
        if matchedQueue.custom_options!=nil and matchedQueue.custom_options.unfreeze_except then
            unfreeze_except = matchedQueue.custom_options.unfreeze_except
        end

        for k, v in pairs(info[1].CreatedEntities) do
            local physCount = v:GetPhysicsObjectCount() - 1
            for i = 0, physCount do
                phys = v:GetPhysicsObjectNum(i)
                if (IsValid(phys)) then
                    local unfreeze = false
                    if unfreeze_except!="" and v:GetClass()!=unfreeze_except then
                        phys:EnableMotion(true) -- Unfreeze the entitiy and all of its objects
                        phys:Wake()
                        unfreeze = true
                    end

                    if not unfreeze then
                        phys:EnableMotion(false)
                        phys:Sleep()
                    end
                end
            end
        end
    end)

    ALX_PC.AssemblyStepsCase = {
        {
            file = "parts/case",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 0, 0),
            case_cover = {
                prop = { Pos = Vector(53, -35.5, -23.75), Ang = Angle(90, 0, 0) },
                prop2 = { Pos = Vector(53, 59.5, 0), Ang = Angle(0, 90, 90) }
            },
            postPaste = function(dupeTable, step)
                if not dupeTable.Entities then return end

                local plugEnts = {}
                for entID, entData in pairs(dupeTable.Entities) do
                    if entData.Model == "models/props_phx/construct/glass/glass_plate2x2.mdl" or entData.Model=="models/props_phx/construct/metal_plate1x2.mdl" then
                        local cover_data = step.case_cover.prop
                        if entData.Model=="models/props_phx/construct/metal_plate1x2.mdl" then
                            cover_data = step.case_cover.prop2
                        end

                        if entData.PhysicsObjects and entData.PhysicsObjects[0] then
                            entData.PhysicsObjects[0].Pos = cover_data.Pos
                            entData.PhysicsObjects[0].Angle = cover_data.Ang
                        end
                    end
                end
            end
        },
        {
            file = "parts/motherboard",
            offsetPos = Vector(12,-19, 5),
            offsetAng = Angle(0, 0, 0)
        },
        {
            file = "parts/cpu",
            offsetPos = Vector(13,-15, 19),
            offsetAng = Angle(0, 0, 0),
            options = {
                unfreeze_except = "gmod_wire_plug",
            }
        },
        {
            file = "parts/gpu",
            offsetPos = Vector(21.3,-34.5, 4.9),
            offsetAng = Angle(90, 0, 0),
            options = {
                unfreeze_except = "gmod_wire_plug",
            }
        },
        {
            file = "devices/hdd",
            offsetPos = Vector(35,20, 0),
            offsetAng = Angle(0, 270, 0)
        },
        {
            file = "parts/hdd controller 4 port pcie",
            offsetPos = Vector(18,-33, -5),
            offsetAng = Angle(90, 0, 0),
            options = {
                unfreeze_except = "gmod_wire_plug",
            }
        },
        {
            file = "cables/sata short",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 90, 0),
            plugs = {
                plug1 = { Pos = Vector(12, -31, 17), Ang = Angle(0, 0, 0) },
                plug2 = { Pos = Vector(-26, -28, 15), Ang = Angle(0, 90, 0) }
            }
        },
        {
            file = "cables/sata short",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 90, 0),
            plugs = {
                plug1 = { Pos = Vector(67, -25, 41), Ang = Angle(0, 0, 0) },
                plug2 = { Pos = Vector(-32, -28, 15), Ang = Angle(0, 90, 0) }
            }
        },
        {
            file = "cables/frontpanel buttons",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 90, 0),
            plugs = {
                plug1 = { Pos = Vector(67, -25, -10), Ang = Angle(0, 0, 0) },
                plug2 = { Pos = Vector(-5, -17, -5), Ang = Angle(0, 90, 0) }
            }
        },
        {
            file = "parts/usb controller",
            offsetPos = Vector(14,-43, 8),
            offsetAng = Angle(90, 0, 0),
            options = {
                unfreeze_except = "gmod_wire_plug",
            }
        },
        {
            file = "cables/frontpanel usb",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 90, 0),
            plugs = {
                plug1 = { Pos = Vector(67, -25, 0), Ang = Angle(0, 0, 0) },
                plug2 = { Pos = Vector(-39, -16, -6), Ang = Angle(0, 90, 0) }
            }
        },
        {
            file = "devices/egp screen",
            offsetPos = Vector(120, -30, 0),
            offsetAng = Angle(0, 0, 0),
            options = {
                unfreeze_except = "gmod_wire_egp",
            }
        },
        {
            file = "cables/hdmi",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 90, 0),
            plugs = {
                plug1 = { Pos = Vector(-45, -23, -5), Ang = Angle(0, 0, 0) },
                plug2 = { Pos = Vector(-32, -112, -10), Ang = Angle(0, 270, 0) }
            }
        },
        {
            file = "devices/cd drives/mini_bd_rom drive",
            offsetPos = Vector(-100, 40, 0),
            offsetAng = Angle(0, 180, 0),
        },
        {
            file = "cables/sata medium",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 90, 0),
            plugs = {
                plug1 = { Pos = Vector(83, -22, 27), Ang = Angle(0, 180, 0) },
                plug2 = { Pos = Vector(63, 100, -33), Ang = Angle(0, 180, 90) }
            }
        },
        {
            file = "devices/ps_2 keyboard",
            offsetPos = Vector(125, -28, -20),
            offsetAng = Angle(0, 0, 0),
            plugs = {
                plug1 = { Pos = Vector(-112, -18, 50), Ang = Angle(0, 90, 90) },
            }
        },
    }

    ALX_PC.AssemblyStepsMin = {
        {
            file = "parts/motherboard",
            offsetPos = Vector(12,-19, 5),
            offsetAng = Angle(0, 0, 0)
        },
        {
            file = "parts/cpu",
            offsetPos = Vector(13,-15, 19),
            offsetAng = Angle(0, 0, 0),
            options = {
                unfreeze_except = "gmod_wire_plug",
            }
        },
        {
            file = "parts/gpu",
            offsetPos = Vector(21.3,-34.5, 4.9),
            offsetAng = Angle(90, 0, 0),
            options = {
                unfreeze_except = "gmod_wire_plug",
            }
        },
        {
            file = "devices/hdd",
            offsetPos = Vector(35,20, 0),
            offsetAng = Angle(0, 270, 0)
        },
        {
            file = "parts/hdd controller 4 port pcie",
            offsetPos = Vector(18,-33, -5),
            offsetAng = Angle(90, 0, 0),
            options = {
                unfreeze_except = "gmod_wire_plug",
            }
        },
        {
            file = "cables/sata short",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 90, 0),
            plugs = {
                plug1 = { Pos = Vector(12, -31, 17), Ang = Angle(0, 0, 0) },
                plug2 = { Pos = Vector(-26, -28, 15), Ang = Angle(0, 90, 0) }
            }
        },
        {
            file = "cables/frontpanel buttons",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 90, 0),
            plugs = {
                plug1 = { Pos = Vector(18, -85, -15), Ang = Angle(0, 270, 0) },
                plug2 = { Pos = Vector(-5, -17, -5), Ang = Angle(0, 90, 0) }
            }
        },
        {
            file = "parts/frontpanel buttons",
            offsetPos = Vector(100, 15, 0),
            offsetAng = Angle(0, 270, 0),
        },
        {
            file = "devices/egp screen",
            offsetPos = Vector(120, -30, 0),
            offsetAng = Angle(0, 0, 0),
            options = {
                unfreeze_except = "gmod_wire_egp",
            }
        },
        {
            file = "cables/hdmi",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 90, 0),
            plugs = {
                plug1 = { Pos = Vector(-45, -23, -5), Ang = Angle(0, 0, 0) },
                plug2 = { Pos = Vector(-32, -112, -10), Ang = Angle(0, 270, 0) }
            }
        },
        {
            file = "devices/cd drives/mini_bd_rom drive",
            offsetPos = Vector(-100, 40, 0),
            offsetAng = Angle(0, 180, 0),
        },
        {
            file = "cables/sata medium",
            offsetPos = Vector(0, 0, 0),
            offsetAng = Angle(0, 90, 0),
            plugs = {
                plug1 = { Pos = Vector(-30, -28, -2), Ang = Angle(0, 90, 0) },
                plug2 = { Pos = Vector(63, 100, -33), Ang = Angle(0, 180, 90) }
            }
        },
        {
            file = "devices/ps_2 keyboard",
            offsetPos = Vector(125, -28, -20),
            offsetAng = Angle(0, 0, 0),
            plugs = {
                plug1 = { Pos = Vector(-112, -18, 50), Ang = Angle(0, 90, 90) },
            }
        },
    }

    local function ModifyCablePositions(dupeTable, plugSettings)
        if not plugSettings or not dupeTable.Entities then return end

        local plugEnts = {}
        for entID, entData in pairs(dupeTable.Entities) do
            if entData.Class == "gmod_wire_plug" then
                table.insert(plugEnts, entData)
            end
        end

        if plugEnts[1] and plugSettings.plug1 then
            if plugEnts[1].PhysicsObjects and plugEnts[1].PhysicsObjects[0] then
                plugEnts[1].PhysicsObjects[0].Pos = plugSettings.plug1.Pos
                plugEnts[1].PhysicsObjects[0].Angle = plugSettings.plug1.Ang
            end
        end

        if plugEnts[2] and plugSettings.plug2 then
            if plugEnts[2].PhysicsObjects and plugEnts[2].PhysicsObjects[0] then
                plugEnts[2].PhysicsObjects[0].Pos = plugSettings.plug2.Pos
                plugEnts[2].PhysicsObjects[0].Angle = plugSettings.plug2.Ang
            end
        end
    end

    ALX_PC.stepIndex = 0
    ALX_PC.Steps = {}
    ALX_PC.HitPos = {}
    ALX_PC.AllEntities = {}
    ALX_PC.timerName = ""

    function ALX_PC.NextStep(ply)

        local step = ALX_PC.Steps[ALX_PC.stepIndex]
        if step!=nil then

            timer.Adjust(ALX_PC.timerName, 5)

            local basePos = ALX_PC.HitPos[1]
            local baseAng = ALX_PC.HitPos[2]

            local rotatedOffset = Vector(step.offsetPos)
            rotatedOffset:Rotate(baseAng)

            local currentPos = basePos + rotatedOffset
            local currentAng = baseAng + step.offsetAng

            ALX_PC.SpawnALXDupe(ply, step.file, currentPos, currentAng, function(dupeTable)
                if step.plugs then
                    ModifyCablePositions(dupeTable, step.plugs)
                end

                if step.postPaste then
                    step.postPaste(dupeTable, step)
                end

                dupeTable.custom_options = step.options
            end)

            ALX_PC.stepIndex = ALX_PC.stepIndex + 1
        else
            timer.Destroy(ALX_PC.timerName)

            undo.Create("ALX PC")
            undo.SetPlayer(ply)
            for k,v in pairs(ALX_PC.AllEntities) do
                undo.AddEntity(v)
            end
            undo.Finish()

            ALX_PC.stepIndex = 0
        end

    end

    function ALX_PC.AssembleALXPC(ply, type)
        if not IsValid(ply) then return end

        local basePos = ply:GetEyeTrace().HitPos
        local baseAng = Angle(0, ply:EyeAngles().y + 180, 0)

        ALX_PC.HitPos = {basePos, baseAng}

        AdvDupe2.Notify(ply, "Starting ALX PC Auto-Assembly...", NOTIFY_GENERIC)

        ALX_PC.stepIndex = 1
        ALX_PC.timerName = "ALX_PC_Assembly_" .. ply:UniqueID()

        local Steps
        if type=="alx_pc_assembler_case" then
            Steps = ALX_PC.AssemblyStepsCase
        else
            Steps = ALX_PC.AssemblyStepsMin
        end
        ALX_PC.Steps = Steps
        ALX_PC.AllEntities = {}

        ALX_PC.NextStep(ply)

        timer.Create(ALX_PC.timerName, 5, 1, function()
            if ALX_PC.stepIndex!=0 then
                ALX_PC.stepIndex = 0
            end
        end)

        --[[
        timer.Create(timerName, 0.25, #Steps, function()
            if not IsValid(ply) then
                timer.Remove(timerName)
                return
            end

            local step = Steps[stepIndex]
            if step then

                local rotatedOffset = Vector(step.offsetPos)
                rotatedOffset:Rotate(baseAng)

                local currentPos = basePos + rotatedOffset
                local currentAng = baseAng + step.offsetAng

                ALX_PC.SpawnALXDupe(ply, step.file, currentPos, currentAng, function(dupeTable)
                    if step.plugs then
                        ModifyCablePositions(dupeTable, step.plugs)
                    end

                    if step.postPaste then
                        step.postPaste(dupeTable, step)
                    end

                    dupeTable.custom_options = step.options
                end)

                stepIndex = stepIndex + 1
            end
        end)
        ]]
    end

    net.Receive("ALX_PC_SpawnDupe", function(len, ply)
        if not IsValid(ply) then return end

        local dupeFile = net.ReadString()
        if not dupeFile or dupeFile == "" then return end

        -- Cooldown check
        if ply.ALX_NextDupeSpawn and ply.ALX_NextDupeSpawn > CurTime() then
            AdvDupe2.Notify(ply, "Please wait before spawning another dupe!", NOTIFY_ERROR)
            return
        end

        if ALX_PC.stepIndex!=0 then
            AdvDupe2.Notify(ply, "Already processing, Please wait...", NOTIFY_GENERIC)
            return
        end

        ply.ALX_NextDupeSpawn = CurTime() + 1.0

        if dupeFile == "alx_pc_assembler_case" or dupeFile == "alx_pc_assembler_min" then
            if not game.SinglePlayer() and dupeFile == "alx_pc_assembler_case" then
                if GetConVarNumber("sbox_maxwire_sockets")<50 then
                    net.Start("ALX_PC_SpawnDupe")
                        net.WriteBool(true)
                    net.Send(ply)
                    return
                end
            end

            ALX_PC.AssembleALXPC(ply, dupeFile)
        else
            ALX_PC.SpawnALXDupe(ply, dupeFile)
        end
    end)
end