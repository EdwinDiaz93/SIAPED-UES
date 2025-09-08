import { MenuItem } from '@/interfaces';
import { Link } from '@inertiajs/react';
import {
    AppBar,
    Box,
    CssBaseline,
    Divider,
    Drawer,
    IconButton,
    List,
    ListItem,
    ListItemIcon,
    ListItemText,
    Toolbar,
    Typography,
} from '@mui/material';
import { useState } from 'react';

interface Props {
    children: React.ReactNode;
}

const drawerWidth = 200;

const MainLayout = ({ children }: Props) => {
    const [mobileOpen, setMobileOpen] = useState(false);

    const menu: MenuItem[] = [
        {
            label: 'Usuarios',
            href: '/',
        },
        {
            label: 'Labor Academica',
            href: '/labor-academica',
        },
    ];

    const handleDrawerToggle = () => {
        setMobileOpen(!mobileOpen);
    };

    const drawer = (
        <div>
            <Toolbar>
                <Typography variant="h5" noWrap component="div">
                    SIAPED UES
                </Typography>
            </Toolbar>{' '}
            {/* solo si quieres dejar espacio para el AppBar */}
            <Divider />
            <List>
                {menu.map((item: MenuItem) => (
                    <ListItem key={item.href} component={Link} href={item.href}>
                        <ListItemIcon>{/* <HomeIcon /> */}</ListItemIcon>
                        <ListItemText primary={item.label} />
                    </ListItem>
                ))}
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
                    <Toolbar className="flex flex-row flex-wrap items-center justify-end bg-white text-black">
                        <IconButton aria-label="menu" edge="start" onClick={handleDrawerToggle} sx={{ mr: 2, display: { sm: 'none' } }}>
                            {/* <MenuIcon /> */}
                        </IconButton>
                    </Toolbar>
                </AppBar>
                <Divider />
                {/* Page content */}
                <Box sx={{ p: 3 }} className="h-screen bg-white">
                    {children}
                </Box>
            </Box>
        </Box>
    );
};
export default MainLayout;
