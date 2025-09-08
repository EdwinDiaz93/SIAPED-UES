import { useState } from "react";
import {AppBar, Box, CssBaseline, Divider, Drawer, IconButton, List, ListItem, ListItemIcon, ListItemText, Toolbar, Typography} from '@mui/material';
import { Link } from "@inertiajs/react";


interface Props{
    children:React.ReactNode
}

const drawerWidth = 200;

const MainLayout = ({children}:Props) =>{
  const [mobileOpen, setMobileOpen] =   useState(false);

  const handleDrawerToggle = () => {
    setMobileOpen(!mobileOpen);
  };

  const drawer = (
    <div>
      <Toolbar >
        <Typography variant="h5" noWrap component="div">
              SIAPED UES
            </Typography>
        </Toolbar> {/* solo si quieres dejar espacio para el AppBar */}
      <Divider />
      <List>
        <ListItem  component={Link} href="/">
          <ListItemIcon>
            {/* <HomeIcon /> */}
          </ListItemIcon>
          <ListItemText primary="Usuarios" />
        </ListItem>
        <ListItem  component={Link} href="/labor-academica">
          <ListItemIcon>
            {/* <HomeIcon /> */}
          </ListItemIcon>
          <ListItemText primary="Labor Academica" />
        </ListItem>
      </List>
    </div>
  );

  return (
    <Box sx={{ display: 'flex' }}>
      <CssBaseline />

      {/* Sidebar */}
      <Drawer
        variant="permanent"
        sx={{
          width: drawerWidth,
          flexShrink: 0,
          '& .MuiDrawer-paper': {
            width: drawerWidth,
            boxSizing: 'border-box',
            top: 0, // para que llegue hasta arriba
            height: '100vh', // ocupa toda la altura
          },
        }}
        open
      >
        {drawer}
      </Drawer>

      {/* Main content with Topbar */}
      <Box
        component="main"
        sx={{
          flexGrow: 1,

        }}
      >
        {/* Topbar */}
        <AppBar
          position="static"
          sx={{
            width: `calc(100%)`,
            zIndex: (theme) => theme.zIndex.drawer + 1,
          }}
        >
          <Toolbar className="bg-white text-black flex flex-row flex-wrap justify-end items-center ">
            <IconButton

              aria-label="menu"
              edge="start"
              onClick={handleDrawerToggle}
              sx={{ mr: 2, display: { sm: 'none' } }}
            >
              {/* <MenuIcon /> */}
            </IconButton>

          </Toolbar>
        </AppBar>

        {/* Page content */}
        <Box sx={{ p: 3 }} className="h-screen">
          {children}
        </Box>
      </Box>
    </Box>
  );
}
export default MainLayout
